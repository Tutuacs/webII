<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

ensure_session_started();

$page_title = 'Pedido Confirmado - Obrigado!';
$pedidoId = isset($_GET['pedido_id']) ? (int)$_GET['pedido_id'] : 0;

// Tenta buscar o pedido
$pedido = null;
if ($pedidoId > 0) {
    try {
        $pedido = $factory->getPedidoDao()->buscaPorId($pedidoId);
    } catch (Throwable $e) {
        // Pedido não encontrado
    }
}

$flashMessage = pull_flash_message();

include_once __DIR__ . '/../Common/layout_header.php';
?>

<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <?php if ($flashMessage) { ?>
                <div class="alert alert-<?php echo $flashMessage['type']; ?> alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?php echo htmlspecialchars($flashMessage['message'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php } ?>

            <div class="panel panel-success" style="border-color: #5cb85c;">
                <div class="panel-heading" style="background-color: #5cb85c; border-color: #5cb85c;">
                    <h3 class="panel-title" style="color: white;">
                        <span class="glyphicon glyphicon-ok-circle"></span> Pedido Confirmado com Sucesso!
                    </h3>
                </div>
                <div class="panel-body text-center">
                    <div style="margin: 30px 0;">
                        <span class="glyphicon glyphicon-ok-circle" style="font-size: 80px; color: #5cb85c;"></span>
                    </div>

                    <h2>Obrigado pela sua compra!</h2>
                    
                    <?php if ($pedido) { ?>
                        <div style="margin: 30px 0;">
                            <p><strong>Número do Pedido:</strong> <span style="font-size: 18px; color: #5cb85c;">#<?php echo (int)$pedido->getId(); ?></span></p>
                            <p><strong>Data do Pedido:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido->getDataPedido())); ?></p>
                            <p><strong>Data de Entrega Estimada:</strong> <?php echo date('d/m/Y', strtotime($pedido->getDataEntrega())); ?></p>
                            <p><strong>Situação:</strong> <span class="label label-warning"><?php echo htmlspecialchars($pedido->getSituacao(), ENT_QUOTES, 'UTF-8'); ?></span></p>
                        </div>
                    <?php } ?>

                    <div style="background-color: #f5f5f5; padding: 20px; border-radius: 5px; margin: 30px 0;">
                        <p>
                            <strong>Um email de confirmação foi enviado para você.</strong><br>
                            Você pode acompanhar seu pedido na seção "Meus Pedidos" da sua conta.
                        </p>
                    </div>

                    <div style="margin-top: 30px;">
                        <a href="/index.php" class="btn btn-default">
                            <span class="glyphicon glyphicon-shopping-cart"></span> Continuar Comprando
                        </a>
                        <a href="/Pages/Orders/my_orders.php" class="btn btn-primary">
                            <span class="glyphicon glyphicon-th-list"></span> Meus Pedidos
                        </a>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <h4><span class="glyphicon glyphicon-info-sign"></span> Próximos Passos</h4>
                <ul>
                    <li>Você receberá um email de confirmação em breve.</li>
                    <li>Acompanhe o status do seu pedido em "Meus Pedidos".</li>
                    <li>Preparamos seu pedido com cuidado e o enviaremos em breve!</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
