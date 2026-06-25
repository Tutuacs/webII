<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

//Apenas users logados (seja cliente ou admin) podem entrar
ensure_session_started();
if (!isset($_SESSION['nome_usuario'])) {
    header('Location: /Pages/Login/index.php');
    exit;
}

$page_title = 'Meus Pedidos';
include_once __DIR__ . '/../Common/layout_header.php';

// Busca os pedidos do utilizador logado no banco de dados
$nomeSessao = $_SESSION['nome_usuario'];
$pedidos = [];

try {
    $conn = $factory->getConnection();
    
    $sql = "SELECT p.id as pedido_numero, p.data_pedido, p.data_entrega, p.situacao, c.nome
            FROM pedido p
            INNER JOIN cliente c ON p.cliente_id = c.id
            WHERE c.nome = :nome_sessao
            ORDER BY p.id DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute([':nome_sessao' => $nomeSessao]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $erro = "Erro ao carregar seus pedidos.";
}
?>

<div class="container" style="margin-top: 30px; min-height: 50vh;">
    <h2><span class="glyphicon glyphicon-shopping-cart"></span> Meus Pedidos</h2>
    <hr>

    <?php if (isset($erro)): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php elseif (empty($pedidos)): ?>
        <div class="alert alert-info">
            Você ainda não realizou nenhuma compra. <a href="/index.php" class="alert-link">Clique aqui para ver nossos produtos!</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nº do Pedido</th>
                        <th>Data da Compra</th>
                        <th>Previsão de Entrega</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><strong>#<?php echo $pedido['pedido_numero']; ?></strong></td>
                            <td><?php echo date('d/m/Y', strtotime($pedido['data_pedido'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($pedido['data_entrega'])); ?></td>
                            <td><span class="label label-info"><?php echo $pedido['situacao']; ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>