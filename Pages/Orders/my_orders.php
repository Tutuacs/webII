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

<style>
    .modal-body {
        background-color: #fcfcfc;
    }

    .carousel-control {
        background-image: none !important;
        color: #333 !important;
    }

    .carousel-control:hover {
        color: #000 !important;
    }

    .list-group-item {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px !important;
        margin-bottom: 5px;
    }
</style>

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
                                <th style="text-align: center;">Situação</th>
                                <th style="text-align: right; padding-right: 15px;">Ações</th>
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
                                    <td style="text-align: center;">
                                        <span class="label <?php echo $labelClass; ?>" style="font-size: 11px; padding: 4px 8px;">
                                            <?php echo htmlspecialchars($pedido['situacao'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right; padding-right: 15px;">
                                        <button class="btn btn-xs btn-primary" onclick="abrirDetalhesCliente(<?php echo $pedido['pedido_numero']; ?>)">
                                            <span class="glyphicon glyphicon-eye-open"></span> Ver Detalhes
                                        </button>
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

<div class="modal fade" id="modalDetalhes" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Detalhes do Pedido <span id="modalPedidoNumero"></span></h4>
            </div>
            <div class="modal-body">
                
                <div id="carrosselProdutos" class="carousel slide" data-ride="carousel" style="margin-bottom: 20px; background: #f8f8f8; text-align: center;">
                    <div class="carousel-inner" id="carrosselInner"></div>
                    <a class="left carousel-control" href="#carrosselProdutos" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <a class="right carousel-control" href="#carrosselProdutos" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
                </div>

                <h5>Itens Comprados:</h5>
                <ul class="list-group" id="listaItensPedido"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
function abrirDetalhesCliente(idDoPedido) {
    document.getElementById('modalPedidoNumero').innerText = '#' + idDoPedido;

    const lista = document.getElementById('listaItensPedido');
    const carrossel = document.getElementById('carrosselInner');
    
    lista.innerHTML = '<li class="list-group-item text-center"><strong>Carregando itens...</strong></li>';
    carrossel.innerHTML = '';
    $('#modalDetalhes').modal('show');

    fetch('/Service/Orders/api_itens_pedido.php?pedido_id=' + idDoPedido)
        .then(response => response.json())
        .then(data => {
            lista.innerHTML = '';
            carrossel.innerHTML = '';

            if (data.itens && data.itens.length > 0) {
                data.itens.forEach((item, i) => {
                    const valorTotalItem = (item.quantidade * item.preco).toFixed(2).replace('.', ',');
                    const precoUnitario = parseFloat(item.preco).toFixed(2).replace('.', ',');
                    
                    lista.innerHTML += `
                        <li class="list-group-item" style="border-left: 5px solid #31708f; margin-bottom: 10px; border-radius: 4px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div style="text-align: left; flex-grow: 1;">
                                    <h5 style="margin: 0 0 4px 0; font-weight: bold; color: #333;">${item.produto_nome}</h5>
                                    <small style="color: #666; display: block; margin-bottom: 8px;">${item.produto_descricao}</small>
                                </div>
                                <span class="label label-info" style="font-size: 13px; padding: 6px 12px; margin-left: 15px; flex-shrink: 0;">
                                    Qtd: ${item.quantidade}
                                </span>
                            </div>
                            <div style="border-top: 1px solid #eee; padding-top: 8px; font-size: 13px; color: #555; text-align: left;">
                                Unit: R$ ${precoUnitario} | <strong>Total: R$ ${valorTotalItem}</strong>
                            </div>
                        </li>
                    `;

                    const activeClass = i === 0 ? 'active' : '';
                    const srcFoto = item.foto_base64 ? `data:image/jpeg;base64,${item.foto_base64}` : 'https://via.placeholder.com/400x200?text=Sem+Foto';
                    
                    carrossel.innerHTML += `
                        <div class="item ${activeClass}">
                            <img src="${srcFoto}" alt="Foto Produto" style="margin: 0 auto; max-height: 200px;">
                            <div class="carousel-caption" style="color: #333; background: rgba(255,255,255,0.8); border-radius: 4px; padding: 2px 10px;">
                                ${item.produto_nome}
                            </div>
                        </div>
                    `;
                });
            } else {
                lista.innerHTML = '<li class="list-group-item">Nenhum item encontrado.</li>';
                carrossel.innerHTML = '<div class="item active"><img src="https://via.placeholder.com/400x200?text=Sem+Itens" style="margin: 0 auto;"></div>';
            }
        })
        .catch(error => {
            console.error("Erro no AJAX:", error);
            lista.innerHTML = '<li class="list-group-item text-danger">Erro ao carregar os itens.</li>';
        });
}
</script>

<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>