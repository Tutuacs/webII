<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

// Apenas users logados podem entrar
ensure_session_started();
if (!isset($_SESSION['id_usuario']) && !isset($_SESSION['nome_usuario'])) {
    header('Location: /Pages/Login/index.php');
    exit;
}

$page_title = 'Meus Pedidos';
include_once __DIR__ . '/../Common/layout_header.php';

$nomeSessao = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$clienteId = isset($_SESSION['cliente_id']) ? (int)$_SESSION['cliente_id'] : null;
$pedidos = [];

try {
    $conn = $factory->getConnection();
    
    // Se tivermos o cliente_id na sessão, filtramos direto pelo ID (Muito mais seguro e rápido!)
    if ($clienteId) {
        $sql = "SELECT p.id as pedido_numero, p.data_pedido, p.data_entrega, p.situacao, c.nome
                FROM pedido p
                INNER JOIN cliente c ON p.cliente_id = c.id
                WHERE p.cliente_id = :cliente_id
                ORDER BY p.id DESC";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute([':cliente_id' => $clienteId]);
    } else {
        // Fallback caso o cliente_id não esteja na sessão por algum motivo
        $sql = "SELECT p.id as pedido_numero, p.data_pedido, p.data_entrega, p.situacao, c.nome
                FROM pedido p
                INNER JOIN cliente c ON p.cliente_id = c.id
                WHERE c.nome = :nome_sessao
                ORDER BY p.id DESC";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute([':nome_sessao' => $nomeSessao]);
    }
    
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $erro = "Erro ao carregar seus pedidos no sistema.";
}
?>

<div class="container" style="margin-top: 30px; margin-bottom: 50px; min-height: 50vh;">
    <h2><span class="glyphicon glyphicon-shopping-cart"></span> Meus Pedidos</h2>
    <hr>

    <?php if (isset($erro)): ?>
        <div class="alert alert-danger">
            <span class="glyphicon glyphicon-exclamation-sign"></span> <?php echo $erro; ?>
        </div>
    <?php elseif (empty($pedidos)): ?>
        <div class="alert alert-info">
            <span class="glyphicon glyphicon-info-sign"></span> Você ainda não realizou nenhuma compra. 
            <a href="/index.php" class="alert-link">Clique aqui para ver nossos produtos!</a>
        </div>
    <?php else: ?>
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Histórico de Compras</h3>
            </div>
            <div class="panel-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th style="padding-left: 15px;">Nº do Pedido</th>
                                <th style="text-align: center;">Data da Compra</th>
                                <th style="text-align: center;">Previsão de Entrega</th>
                                <th style="text-align: right; padding-right: 15px;">Situação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): 
                                // Muda a cor da tag/label baseado no status do pedido
                                $labelClass = 'label-default';
                                $status = strtolower($pedido['situacao']);
                                if (strpos($status, 'pago') !== false || strpos($status, 'entregue') !== false || strpos($status, 'confirmado') !== false) {
                                    $labelClass = 'label-success';
                                } elseif (strpos($status, 'pendente') !== false || strpos($status, 'processamento') !== false) {
                                    $labelClass = 'label-warning';
                                } elseif (strpos($status, 'cancelado') !== false) {
                                    $labelClass = 'label-danger';
                                }
                            ?>
                                <tr>
                                    <td style="padding-left: 15px;"><strong>#<?php echo $pedido['pedido_numero']; ?></strong></td>
                                    <td style="text-align: center;"><?php echo date('d/m/Y', strtotime($pedido['data_pedido'])); ?></td>
                                    <td style="text-align: center;">
                                        <?php echo $pedido['data_entrega'] ? date('d/m/Y', strtotime($pedido['data_entrega'])) : 'Em breve'; ?>
                                    </td>
                                    <td style="text-align: right; padding-right: 15px;">
                                        <span class="label <?php echo $labelClass; ?>" style="font-size: 11px; padding: 4px 8px;">
                                            <?php echo htmlspecialchars($pedido['situacao'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>