<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';
require_once __DIR__ . '/../../Service/Cart/CartService.php';

ensure_session_started();

$page_title = 'Carrinho de Compras';
$cartService = new CartService($factory);
$cartItems = $cartService->getCartItems();
$cartTotal = $cartService->getCartTotal();
$isLogged = isset($_SESSION['id_usuario']);

// Mensagens de feedback
$flashMessage = pull_flash_message();

include_once __DIR__ . '/../Common/layout_header.php';
?>

<div class="container" style="margin-top: 30px;">
    <div class="row">
        <div class="col-md-8">
            <h2>Carrinho de Compras</h2>
            
            <?php if ($flashMessage) { ?>
                <div class="alert alert-<?php echo $flashMessage['type']; ?> alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?php echo htmlspecialchars($flashMessage['message'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php } ?>

            <?php if (empty($cartItems)) { ?>
                <div class="alert alert-info">
                    <p>Seu carrinho está vazio. <a href="/index.php">Voltar ao catálogo</a></p>
                </div>
            <?php } else { ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th style="text-align: center;">Preço</th>
                                <th style="text-align: center;">Quantidade</th>
                                <th style="text-align: center;">Subtotal</th>
                                <th style="text-align: center;">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item) { 
                                $produto = $item['produto'];
                                $estoque = $item['estoque'];
                                $quantidade = $item['quantidade'];
                                $subtotal = $item['subtotal'];
                            ?>
                                <tr class="cart-item" data-produto-id="<?php echo (int)$produto->getId(); ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($produto->getNome(), ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </td>
                                    <td style="text-align: center;">
                                        R$ <?php echo number_format($estoque ? $estoque->getPreco() : 0, 2, ',', '.'); ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="input-group" style="max-width: 120px; margin: 0 auto;">
                                            <span class="input-group-btn">
                                                <button class="btn btn-xs btn-default qty-decrease" type="button">
                                                    <span class="glyphicon glyphicon-minus"></span>
                                                </button>
                                            </span>
                                            <input type="text" class="form-control qty-input" value="<?php echo (int)$quantidade; ?>" readonly style="text-align: center;">
                                            <span class="input-group-btn">
                                                <button class="btn btn-xs btn-default qty-increase" type="button">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                </button>
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            Disponível: <?php echo (int)($estoque ? $estoque->getQuantidade() : 0); ?>
                                        </small>
                                    </td>
                                    <td style="text-align: center;" class="subtotal-cell">
                                        R$ <span class="subtotal-value"><?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="/Service/Products/remove_from_cart.php?produto_id=<?php echo (int)$produto->getId(); ?>" 
                                           class="btn btn-xs btn-danger"
                                           onclick="return confirm('Tem certeza que deseja remover este produto?');">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-6">
                        <a href="/index.php" class="btn btn-default">
                            <span class="glyphicon glyphicon-arrow-left"></span> Continuar Comprando
                        </a>
                    </div>
                    <div class="col-md-6 text-right">
                        <h3>Total: R$ <span id="cart-total"><?php echo number_format($cartTotal, 2, ',', '.'); ?></span></h3>
                        <a href="/Pages/Products/checkout.php" class="btn btn-success btn-lg">
                            <span class="glyphicon glyphicon-ok"></span> Finalizar Compra
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Resumo do Carrinho</h3>
                </div>
                <div class="panel-body">
                    <ul class="list-unstyled">
                        <li>
                            <strong>Produtos:</strong> 
                            <span class="pull-right" id="item-count"><?php echo $cartService->getCartItemCount(); ?></span>
                        </li>
                        <li>
                            <strong>Unidades:</strong> 
                            <span class="pull-right" id="unit-count"><?php echo $cartService->getCartUnitCount(); ?></span>
                        </li>
                        <li style="border-top: 1px solid #ddd; padding-top: 10px; margin-top: 10px;">
                            <strong>Total:</strong>
                            <span class="pull-right text-success" style="font-size: 18px; font-weight: bold;">
                                R$ <span id="summary-total"><?php echo number_format($cartTotal, 2, ',', '.'); ?></span>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <?php if (!empty($cartItems)) { ?>
                <div class="alert alert-info">
                    <small>
                        <strong>ℹ️ Dica:</strong> Você pode aumentar ou diminuir as quantidades diretamente na tabela ao lado. As alterações são salvas automaticamente!
                    </small>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delegação de eventos para os botões +/-
    document.querySelectorAll('.qty-increase, .qty-decrease').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const isIncrease = this.classList.contains('qty-increase');
            const cartItem = this.closest('.cart-item');
            const produtoId = parseInt(cartItem.dataset.produtoId);
            const currentQty = parseInt(cartItem.querySelector('.qty-input').value);
            const novaQty = isIncrease ? currentQty + 1 : currentQty - 1;

            updateCartQuantity(produtoId, novaQty);
        });
    });
});

function updateCartQuantity(produtoId, novaQuantidade) {
    // Requisição AJAX para atualizar quantidade
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/Service/Cart/update_quantity.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (response.success) {
                    // Atualiza a quantidade no formulário
                    const cartItem = document.querySelector('[data-produto-id="' + produtoId + '"]');
                    if (novaQuantidade === 0) {
                        cartItem.remove();
                    } else {
                        cartItem.querySelector('.qty-input').value = novaQuantidade;
                        
                        // Atualiza o subtotal
                        const subtotalCell = cartItem.querySelector('.subtotal-value');
                        const precoText = cartItem.querySelectorAll('td')[1].innerText;
                        const preco = parseFloat(precoText.replace('R$ ', '').replace('.', '').replace(',', '.'));
                        const novoSubtotal = (novaQuantidade * preco).toFixed(2);
                        
                        subtotalCell.innerText = novoSubtotal.replace('.', ',');
                    }
                    
                    // Atualiza o total
                    document.getElementById('cart-total').innerText = 
                        response.novoTotal.toFixed(2).replace('.', ',');
                    document.getElementById('summary-total').innerText = 
                        response.novoTotal.toFixed(2).replace('.', ',');
                    
                } else {
                    alert('Erro: ' + response.message);
                }
            } catch (e) {
                console.error('Erro ao processar resposta:', e);
            }
        }
    };
    
    xhr.send('produto_id=' + produtoId + '&quantidade=' + novaQuantidade);
}
</script>

<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
