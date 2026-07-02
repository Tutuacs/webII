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

<style>
    /* =====================================================================
       Namespace isolado para não herdar cores/tema global (ex: amarelo)
       ===================================================================== */
    .cart-page, .cart-page * {
        box-sizing: border-box;
    }

    .cart-page {
        --cp-card: #ffffff;
        --cp-border: #e7e9ee;
        --cp-text: #1f2430;
        --cp-text-muted: #7a8291;
        --cp-accent: #3454d1;
        --cp-success: #1f9d55;
        --cp-success-dark: #167a42;
        --cp-danger: #e5484d;
        --cp-danger-bg: #fdecec;

        margin-top: 28px;
        color: var(--cp-text);
        font-family: -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    .cart-page h2 {
        font-weight: 700;
        font-size: 24px;
        margin-bottom: 20px;
        color: var(--cp-text);
    }

    /* ---- Notificações (substitui alert-info do tema) ---- */
    .cp-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #eef2ff;
        border: 1px solid #dbe2fb;
        border-left: 4px solid var(--cp-accent);
        color: #2c3568;
        padding: 12px 14px;
        border-radius: 8px;
        margin-bottom: 18px;
        font-size: 13.5px;
    }
    .cp-note.cp-note-success {
        background: #eafaf1;
        border-color: #cdf0dd;
        border-left-color: var(--cp-success);
        color: #145c33;
    }
    .cp-note.cp-note-danger {
        background: var(--cp-danger-bg);
        border-color: #f8d3d4;
        border-left-color: var(--cp-danger);
        color: #8a1c1f;
    }
    .cp-note-icon { flex-shrink: 0; line-height: 1; margin-top: 1px; }
    .cp-note-close {
        margin-left: auto;
        background: none;
        border: none;
        cursor: pointer;
        color: inherit;
        opacity: .55;
        font-size: 16px;
        line-height: 1;
    }
    .cp-note-close:hover { opacity: 1; }

    /* ---- Estado vazio ---- */
    .cp-empty {
        background: var(--cp-card);
        border: 1px dashed var(--cp-border);
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        color: var(--cp-text-muted);
    }
    .cp-empty a {
        color: var(--cp-accent);
        font-weight: 600;
        text-decoration: none;
    }
    .cp-empty a:hover { text-decoration: underline; }

    /* ---- Listagem de itens em cards ---- */
    .cp-items {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .cp-item {
        display: flex;
        align-items: center;
        gap: 16px;
        background: var(--cp-card);
        border: 1px solid var(--cp-border);
        border-radius: 12px;
        padding: 14px 16px;
        transition: box-shadow .15s ease, border-color .15s ease;
    }
    .cp-item:hover {
        box-shadow: 0 4px 14px rgba(20, 24, 40, 0.07);
        border-color: #d7dbe4;
    }
    .cp-item.cp-item-removing {
        opacity: 0.35;
        transform: scale(0.98);
        pointer-events: none;
    }

    .cp-item-avatar {
        flex-shrink: 0;
        width: 46px;
        height: 46px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--cp-accent), #6c7ff0);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 17px;
    }

    .cp-item-info {
        flex: 1 1 auto;
        min-width: 0;
    }

    .cp-item-name {
        font-weight: 600;
        font-size: 14.5px;
        color: var(--cp-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cp-item-price {
        font-size: 12.5px;
        color: var(--cp-text-muted);
        margin-top: 2px;
    }

    .cp-item-stock {
        font-size: 11.5px;
        color: #a3a9b5;
        margin-top: 1px;
    }

    /* ---- Stepper de quantidade ---- */
    .cp-stepper {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        border: 1px solid var(--cp-border);
        border-radius: 999px;
        overflow: hidden;
        background: #fafbfc;
    }

    .cp-stepper button {
        width: 30px;
        height: 30px;
        border: none;
        background: transparent;
        color: var(--cp-text);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color .12s ease;
        padding: 0;
    }
    .cp-stepper button:hover { background: #eef1f5; }
    .cp-stepper button:active { background: #e3e7ee; }
    .cp-stepper button svg { width: 13px; height: 13px; }

    .cp-stepper .qty-input {
        width: 34px;
        border: none;
        background: transparent;
        text-align: center;
        font-weight: 700;
        font-size: 13.5px;
        color: var(--cp-text);
        padding: 0;
    }
    .cp-stepper .qty-input:focus { outline: none; }

    /* ---- Subtotal ---- */
    .cp-item-subtotal {
        flex-shrink: 0;
        min-width: 90px;
        text-align: right;
        font-weight: 700;
        font-size: 14.5px;
        color: var(--cp-text);
        transition: opacity .15s ease;
    }
    .cp-item-subtotal .cur { font-size: 11px; color: var(--cp-text-muted); font-weight: 600; margin-right: 2px; }
    .cp-item.cp-item-updating .cp-item-subtotal { opacity: 0.35; }

    /* ---- Botão remover ---- */
    .cp-remove-btn {
        flex-shrink: 0;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid var(--cp-border);
        background: #fff;
        color: #9aa1ad;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        transition: all .15s ease;
    }
    .cp-remove-btn:hover {
        background: var(--cp-danger-bg);
        border-color: #f3c1c3;
        color: var(--cp-danger);
    }
    .cp-remove-btn svg { width: 15px; height: 15px; }

    /* ---- Rodapé de ações ---- */
    .cp-actions-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-top: 22px;
    }

    .cp-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all .15s ease;
    }
    .cp-btn svg { width: 15px; height: 15px; }

    .cp-btn-ghost {
        background: #fff;
        border-color: var(--cp-border);
        color: var(--cp-text);
    }
    .cp-btn-ghost:hover { background: #f2f3f6; color: var(--cp-text); text-decoration: none; }

    .cp-btn-primary {
        background: var(--cp-success);
        color: #fff;
        padding: 13px 26px;
        font-size: 15px;
        box-shadow: 0 2px 8px rgba(31, 157, 85, 0.28);
    }
    .cp-btn-primary:hover { background: var(--cp-success-dark); color: #fff; text-decoration: none; }

    .cp-total-block { text-align: right; }
    .cp-total-label { font-size: 12.5px; color: var(--cp-text-muted); }
    .cp-total-value { font-size: 24px; font-weight: 800; color: var(--cp-text); margin: 2px 0 10px; }
    .cp-total-value .cur { font-size: 14px; color: var(--cp-text-muted); font-weight: 700; }

    /* ---- Card de resumo ---- */
    .cp-summary-card {
        border: 1px solid var(--cp-border);
        border-radius: 12px;
        overflow: hidden;
        background: var(--cp-card);
        box-shadow: 0 2px 10px rgba(20, 24, 40, 0.04);
    }

    .cp-summary-head {
        background: var(--cp-text);
        color: #fff;
        padding: 14px 18px;
        font-weight: 700;
        font-size: 15px;
        letter-spacing: .2px;
    }

    .cp-summary-body { padding: 18px; }

    .cp-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 9px 0;
        min-width: 0;
        font-size: 13.5px;
    }
    .cp-summary-row .label { color: var(--cp-text-muted); white-space: nowrap; }
    .cp-summary-row .value {
        font-weight: 700;
        color: var(--cp-text);
        text-align: right;
        min-width: 0;
        overflow-wrap: anywhere;
    }
    .cp-summary-row.total-row {
        border-top: 1px solid var(--cp-border);
        margin-top: 6px;
        padding-top: 15px;
    }
    .cp-summary-row.total-row .label { font-size: 14.5px; font-weight: 700; color: var(--cp-text); }
    .cp-summary-row.total-row .value { font-size: 21px; font-weight: 800; color: var(--cp-success); }

    @media (max-width: 767px) {
        .cp-item { flex-wrap: wrap; }
        .cp-item-subtotal { order: 4; width: 100%; text-align: left; margin-top: 6px; }
        .cp-summary-card { margin-top: 20px; }
        .cp-actions-row { flex-direction: column; align-items: stretch; }
        .cp-total-block { text-align: left; }
    }
</style>

<div class="container cart-page">
    <div class="row">
        <div class="col-md-8">
            <h2>Carrinho de Compras</h2>

            <?php if ($flashMessage) { ?>
                <div class="cp-note cp-note-<?php echo $flashMessage['type'] === 'success' ? 'success' : ($flashMessage['type'] === 'danger' ? 'danger' : 'info'); ?>" id="flash-note">
                    <span class="cp-note-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v5M12 16h.01"/></svg>
                    </span>
                    <span><?php echo htmlspecialchars($flashMessage['message'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <button type="button" class="cp-note-close" onclick="document.getElementById('flash-note').remove();">&times;</button>
                </div>
            <?php } ?>

            <?php if (empty($cartItems)) { ?>
                <div class="cp-empty">
                    <p>Seu carrinho está vazio.</p>
                    <a href="/index.php">Voltar ao catálogo</a>
                </div>
            <?php } else { ?>
                <div class="cp-items" id="cart-items-list">
                    <?php foreach ($cartItems as $item) {
                        $produto = $item['produto'];
                        $estoque = $item['estoque'];
                        $quantidade = $item['quantidade'];
                        $subtotal = $item['subtotal'];
                        $nome = $produto->getNome();
                        $inicial = mb_strtoupper(mb_substr($nome, 0, 1, 'UTF-8'), 'UTF-8');
                        $preco = $estoque ? $estoque->getPreco() : 0;
                    ?>
                        <div class="cp-item" data-produto-id="<?php echo (int)$produto->getId(); ?>" data-preco="<?php echo (float)$preco; ?>">
                            <div class="cp-item-avatar"><?php echo htmlspecialchars($inicial, ENT_QUOTES, 'UTF-8'); ?></div>

                            <div class="cp-item-info">
                                <div class="cp-item-name"><?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="cp-item-price">R$ <?php echo number_format($preco, 2, ',', '.'); ?> / un.</div>
                                <div class="cp-item-stock">Disponível: <?php echo (int)($estoque ? $estoque->getQuantidade() : 0); ?></div>
                            </div>

                            <div class="cp-stepper">
                                <button type="button" class="qty-decrease" aria-label="Diminuir quantidade">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M5 12h14"/></svg>
                                </button>
                                <input type="text" class="qty-input" value="<?php echo (int)$quantidade; ?>" readonly>
                                <button type="button" class="qty-increase" aria-label="Aumentar quantidade">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                </button>
                            </div>

                            <div class="cp-item-subtotal">
                                <span class="cur">R$</span><span class="subtotal-value"><?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                            </div>

                            <a href="/Service/Products/remove_from_cart.php?produto_id=<?php echo (int)$produto->getId(); ?>"
                               class="cp-remove-btn"
                               aria-label="Remover produto"
                               onclick="return confirm('Tem certeza que deseja remover este produto?');">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6h16Z"/></svg>
                            </a>
                        </div>
                    <?php } ?>
                </div>

                <div class="cp-actions-row">
                    <a href="/index.php" class="cp-btn cp-btn-ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        Continuar Comprando
                    </a>
                    <div class="cp-total-block">
                        <div class="cp-total-label">Total do pedido</div>
                        <div class="cp-total-value"><span class="cur">R$</span> <span id="cart-total"><?php echo number_format($cartTotal, 2, ',', '.'); ?></span></div>
                        <a href="/Pages/Products/checkout.php" class="cp-btn cp-btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            Finalizar Compra
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="col-md-4">
            <div class="cp-summary-card">
                <div class="cp-summary-head">Resumo do Carrinho</div>
                <div class="cp-summary-body">
                    <div class="cp-summary-row">
                        <span class="label">Produtos</span>
                        <span class="value" id="item-count"><?php echo $cartService->getCartItemCount(); ?></span>
                    </div>
                    <div class="cp-summary-row">
                        <span class="label">Unidades</span>
                        <span class="value" id="unit-count"><?php echo $cartService->getCartUnitCount(); ?></span>
                    </div>
                    <div class="cp-summary-row total-row">
                        <span class="label">Total</span>
                        <span class="value">R$ <span id="summary-total"><?php echo number_format($cartTotal, 2, ',', '.'); ?></span></span>
                    </div>
                </div>
            </div>

            <?php if (!empty($cartItems)) { ?>
                <div class="cp-note" style="margin-top: 16px;">
                    <span class="cp-note-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    </span>
                    <span>Você pode ajustar as quantidades diretamente na lista ao lado. As alterações são salvas automaticamente.</span>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.qty-increase, .qty-decrease').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            const isIncrease = this.classList.contains('qty-increase');
            const cartItem = this.closest('.cp-item');
            const produtoId = parseInt(cartItem.dataset.produtoId, 10);
            const currentQty = parseInt(cartItem.querySelector('.qty-input').value, 10);
            const novaQty = isIncrease ? currentQty + 1 : currentQty - 1;

            if (novaQty < 0) return;

            updateCartQuantity(produtoId, novaQty);
        });
    });
});

function updateCartQuantity(produtoId, novaQuantidade) {
    const cartItem = document.querySelector('.cp-item[data-produto-id="' + produtoId + '"]');
    if (cartItem) cartItem.classList.add('cp-item-updating');

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/Service/Cart/update_quantity.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);

                if (response.success) {
                    if (novaQuantidade === 0) {
                        if (cartItem) {
                            cartItem.classList.add('cp-item-removing');
                            setTimeout(function() {
                                cartItem.remove();
                                if (document.querySelectorAll('.cp-item').length === 0) {
                                    window.location.reload();
                                }
                            }, 150);
                        }
                    } else if (cartItem) {
                        cartItem.classList.remove('cp-item-updating');
                        cartItem.querySelector('.qty-input').value = novaQuantidade;

                        const preco = parseFloat(cartItem.dataset.preco);
                        const novoSubtotal = (novaQuantidade * preco).toFixed(2);
                        cartItem.querySelector('.subtotal-value').innerText = novoSubtotal.replace('.', ',');
                    }

                    if (typeof response.novoTotal !== 'undefined') {
                        const totalFormatado = response.novoTotal.toFixed(2).replace('.', ',');
                        const totalEl = document.getElementById('cart-total');
                        const summaryTotalEl = document.getElementById('summary-total');
                        if (totalEl) totalEl.innerText = totalFormatado;
                        if (summaryTotalEl) summaryTotalEl.innerText = totalFormatado;
                    }

                    atualizarContadoresResumo(response);

                } else {
                    alert('Erro: ' + response.message);
                    if (cartItem) cartItem.classList.remove('cp-item-updating');
                }
            } catch (e) {
                console.error('Erro ao processar resposta:', e);
                if (cartItem) cartItem.classList.remove('cp-item-updating');
            }
        }
    };

    xhr.onerror = function() {
        if (cartItem) cartItem.classList.remove('cp-item-updating');
        alert('Não foi possível atualizar o carrinho. Tente novamente.');
    };

    xhr.send('produto_id=' + produtoId + '&quantidade=' + novaQuantidade);
}

function atualizarContadoresResumo(response) {
    const itemCountEl = document.getElementById('item-count');
    const unitCountEl = document.getElementById('unit-count');

    if (typeof response.novoItemCount !== 'undefined' && typeof response.novoUnitCount !== 'undefined') {
        if (itemCountEl) itemCountEl.innerText = response.novoItemCount;
        if (unitCountEl) unitCountEl.innerText = response.novoUnitCount;
        return;
    }

    const linhas = document.querySelectorAll('.cp-item');
    let unidades = 0;
    linhas.forEach(function(linha) {
        unidades += parseInt(linha.querySelector('.qty-input').value, 10) || 0;
    });

    if (itemCountEl) itemCountEl.innerText = linhas.length;
    if (unitCountEl) unitCountEl.innerText = unidades;
}
</script>

<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>