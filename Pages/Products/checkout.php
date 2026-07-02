<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';
require_once __DIR__ . '/../../Service/Cart/CartService.php';

ensure_session_started();

$page_title = 'Checkout - Finalizar Compra';
$cartService = new CartService($factory);

$validation = $cartService->validateCart();
if (!$validation['success']) {
    header('Location: /Pages/Products/cart.php');
    exit;
}

$cartItems = $cartService->getCartItems();
$cartTotal = $cartService->getCartTotal();
$isLogged = isset($_SESSION['id_usuario']);
$clienteId = isset($_SESSION['cliente_id']) ? (int)$_SESSION['cliente_id'] : null;

// Busca os dados de entrega vinculados ao cliente da sessão
// (funciona tanto para usuário logado quanto para checkout de convidado)
$cliente = null;
$enderecoObj = null;

if ($clienteId) {
    try {
        $cliente = $factory->getClienteDao()->buscaPorId($clienteId);

        if ($cliente) {
            $enderecoId = null;

            if (method_exists($cliente, 'getEnderecoId')) {
                $enderecoId = $cliente->getEnderecoId();
            } elseif (method_exists($cliente, 'getEndereco') && $cliente->getEndereco()) {
                $enderecoId = $cliente->getEndereco()->getId();
            }
            // Sem fallback: se nenhum método existir, é erro de mapeamento da entidade
            // Cliente e precisa ser corrigido lá, não mascarado aqui.

            if ($enderecoId) {
                $enderecoObj = $factory->getEnderecoDao()->buscaPorId($enderecoId);
            }
        }
    } catch (Throwable $e) {
        $cliente = null;
    }
}

// Nome exibido: usa o nome da conta logada; se for checkout de convidado, usa o nome do cliente
$nomeSessao = isset($_SESSION['nome_usuario'])
    ? $_SESSION['nome_usuario']
    : ($cliente ? $cliente->getNome() : '');

include_once __DIR__ . '/../Common/layout_header.php';
?>

<style>
    .checkout-page, .checkout-page * {
        box-sizing: border-box;
    }

    .checkout-page {
        --ck-card: #ffffff;
        --ck-border: #e7e9ee;
        --ck-text: #1f2430;
        --ck-text-muted: #7a8291;
        --ck-accent: #3454d1;
        --ck-accent-bg: #eef2ff;
        --ck-success: #1f9d55;
        --ck-success-bg: #eafaf1;
        --ck-success-dark: #167a42;
        --ck-warning: #b7791f;
        --ck-warning-bg: #fff8e8;
        --ck-danger: #e5484d;

        margin-top: 28px;
        margin-bottom: 50px;
        color: var(--ck-text);
        font-family: -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    .checkout-page h2 {
        font-weight: 700;
        font-size: 24px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .checkout-page h2 svg { width: 22px; height: 22px; color: var(--ck-success); }

    /* ---- Card base ---- */
    .ck-card {
        background: var(--ck-card);
        border: 1px solid var(--ck-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(20, 24, 40, 0.04);
        margin-bottom: 20px;
    }
    .ck-card-head {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--ck-border);
        font-weight: 700;
        font-size: 15px;
    }
    .ck-card-head svg { width: 16px; height: 16px; color: var(--ck-text-muted); }
    .ck-card-body { padding: 18px; }

    /* ---- Itens do pedido ---- */
    .ck-order-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid #f0f1f4;
    }
    .ck-order-item:last-child { border-bottom: none; }

    .ck-order-avatar {
        flex-shrink: 0;
        width: 38px;
        height: 38px;
        border-radius: 9px;
        background: linear-gradient(135deg, var(--ck-accent), #6c7ff0);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
    }

    .ck-order-info { flex: 1 1 auto; min-width: 0; }
    .ck-order-name {
        font-weight: 600;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ck-order-meta { font-size: 12px; color: var(--ck-text-muted); margin-top: 2px; }

    .ck-order-subtotal {
        flex-shrink: 0;
        font-weight: 700;
        font-size: 14.5px;
        min-width: 90px;
        text-align: right;
    }

    .ck-order-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        margin-top: 6px;
        border-top: 1px solid var(--ck-border);
        font-weight: 700;
        font-size: 17px;
    }
    .ck-order-total-row .amount { color: var(--ck-success); font-size: 20px; }

    /* ---- Notas / alertas ---- */
    .ck-note {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 20px;
        font-size: 13.5px;
    }
    .ck-note svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px; }
    .ck-note h4 { margin: 0 0 4px; font-size: 14.5px; font-weight: 700; }
    .ck-note p { margin: 0; }

    .ck-note-warning { background: var(--ck-warning-bg); color: #7a5518; border: 1px solid #f3e2b8; }
    .ck-note-warning svg { color: var(--ck-warning); }
    .ck-note-success { background: var(--ck-success-bg); color: #145c33; border: 1px solid #cdf0dd; }
    .ck-note-success svg { color: var(--ck-success); }
    .ck-note-info { background: var(--ck-accent-bg); color: #2c3568; border: 1px solid #dbe2fb; }
    .ck-note-info svg { color: var(--ck-accent); }

    /* ---- Opções (login / novo cliente) ---- */
    .ck-options {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .ck-option {
        flex: 1 1 260px;
        background: var(--ck-card);
        border: 1px solid var(--ck-border);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .ck-option-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--ck-accent-bg);
        color: var(--ck-accent);
    }
    .ck-option.ck-option-success .ck-option-icon { background: var(--ck-success-bg); color: var(--ck-success); }
    .ck-option-icon svg { width: 20px; height: 20px; }
    .ck-option h3 { margin: 0; font-size: 15.5px; font-weight: 700; }
    .ck-option p { margin: 0; color: var(--ck-text-muted); font-size: 13.5px; flex: 1; }

    /* ---- Endereço ---- */
    .ck-address-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }
    .ck-field { margin-bottom: 10px; font-size: 13.5px; }
    .ck-field .k { color: var(--ck-text-muted); display: block; font-size: 11.5px; text-transform: uppercase; letter-spacing: .03em; margin-bottom: 2px; }
    .ck-field .v { font-weight: 600; }

    @media (max-width: 767px) {
        .ck-address-grid { grid-template-columns: 1fr; }
    }

    /* ---- Botões ---- */
    .ck-btn {
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
    .ck-btn svg { width: 15px; height: 15px; }
    .ck-btn-block { width: 100%; justify-content: center; }

    .ck-btn-ghost { background: #fff; border-color: var(--ck-border); color: var(--ck-text); }
    .ck-btn-ghost:hover { background: #f2f3f6; color: var(--ck-text); text-decoration: none; }

    .ck-btn-primary { background: var(--ck-accent); color: #fff; }
    .ck-btn-primary:hover { background: #2a41a8; color: #fff; text-decoration: none; }

    .ck-btn-success { background: var(--ck-success); color: #fff; }
    .ck-btn-success:hover { background: var(--ck-success-dark); color: #fff; text-decoration: none; }

    .ck-btn-lg { padding: 13px 24px; font-size: 15px; }

    .ck-btn-outline { background: #fff; border-color: #cfd6f5; color: var(--ck-accent); }
    .ck-btn-outline:hover { background: var(--ck-accent-bg); color: var(--ck-accent); text-decoration: none; }

    .ck-actions-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    /* ---- Sidebar resumo ---- */
    .ck-summary-card {
        border: 1px solid var(--ck-border);
        border-radius: 12px;
        overflow: hidden;
        background: var(--ck-card);
        box-shadow: 0 2px 10px rgba(20, 24, 40, 0.04);
    }
    .ck-summary-head {
        background: var(--ck-text);
        color: #fff;
        padding: 14px 18px;
        font-weight: 700;
        font-size: 15px;
    }
    .ck-summary-body { padding: 18px; }
    .ck-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 9px 0;
        font-size: 13.5px;
    }
    .ck-summary-row .label { color: var(--ck-text-muted); white-space: nowrap; }
    .ck-summary-row .value { font-weight: 700; text-align: right; overflow-wrap: anywhere; }
    .ck-summary-row.total-row {
        border-top: 1px solid var(--ck-border);
        margin-top: 6px;
        padding-top: 15px;
    }
    .ck-summary-row.total-row .label { font-size: 14.5px; font-weight: 700; color: var(--ck-text); }
    .ck-summary-row.total-row .value { font-size: 21px; font-weight: 800; color: var(--ck-success); }

    .ck-summary-status {
        margin-top: 14px;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 12.5px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }
    .ck-summary-status svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; }
    .ck-summary-status.ok { background: var(--ck-success-bg); color: #145c33; }
    .ck-summary-status.ok svg { color: var(--ck-success); }
    .ck-summary-status.pending { background: var(--ck-warning-bg); color: #7a5518; }
    .ck-summary-status.pending svg { color: var(--ck-warning); }
</style>

<div class="container checkout-page">
    <div class="row">
        <div class="col-md-8">
            <h2>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
                Confirmação do Pedido
            </h2>

            <div class="ck-card">
                <div class="ck-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
                    Resumo dos Produtos
                </div>
                <div class="ck-card-body">
                    <?php foreach ($cartItems as $item) {
                        $produto = $item['produto'];
                        $estoque = $item['estoque'];
                        $quantidade = $item['quantidade'];
                        $subtotal = $item['subtotal'];
                        $nomeProduto = $produto->getNome();
                        $inicial = mb_strtoupper(mb_substr($nomeProduto, 0, 1, 'UTF-8'), 'UTF-8');
                    ?>
                        <div class="ck-order-item">
                            <div class="ck-order-avatar"><?php echo htmlspecialchars($inicial, ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="ck-order-info">
                                <div class="ck-order-name"><?php echo htmlspecialchars($nomeProduto, ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="ck-order-meta"><?php echo (int)$quantidade; ?> un. &times; R$ <?php echo number_format($estoque ? $estoque->getPreco() : 0, 2, ',', '.'); ?></div>
                            </div>
                            <div class="ck-order-subtotal">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></div>
                        </div>
                    <?php } ?>

                    <div class="ck-order-total-row">
                        <span>Total</span>
                        <span class="amount">R$ <?php echo number_format($cartTotal, 2, ',', '.'); ?></span>
                    </div>
                </div>
            </div>

            <?php if (!$cliente && !$isLogged) { ?>
                <div class="ck-note ck-note-warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><path d="M12 9v4M12 17h.01"/></svg>
                    <div>
                        <h4>Você precisa estar conectado ou informar seus dados de entrega</h4>
                        <p>Escolha uma opção abaixo para continuar.</p>
                    </div>
                </div>

                <div class="ck-options">
                    <div class="ck-option">
                        <div class="ck-option-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                        </div>
                        <h3>Cliente Existente</h3>
                        <p>Se você já é nosso cliente, faça login para continuar com seus dados salvos.</p>
                        <a href="/Pages/Login/index.php?redirect=/Pages/Products/checkout.php" class="ck-btn ck-btn-primary ck-btn-block">
                            Fazer Login
                        </a>
                    </div>
                    <div class="ck-option ck-option-success">
                        <div class="ck-option-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
                        </div>
                        <h3>Novo Cliente</h3>
                        <p>Se você é novo por aqui, cadastre seus dados de entrega para finalizar como convidado.</p>
                        <a href="/Pages/Addresses/create.php?checkout=1" class="ck-btn ck-btn-success ck-btn-block">
                            Cadastrar Endereço
                        </a>
                    </div>
                </div>

                <div class="ck-actions-row">
                    <a href="/Pages/Products/cart.php" class="ck-btn ck-btn-ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        Voltar ao Carrinho
                    </a>
                </div>

            <?php } elseif ($cliente) { ?>
                <div class="ck-note ck-note-success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
                    <div>
                        <h4>Dados de Entrega Confirmados!</h4>
                        <p>Olá, <?php echo htmlspecialchars($nomeSessao, ENT_QUOTES, 'UTF-8'); ?>. Revise os dados de envio abaixo.</p>
                    </div>
                </div>

                <div class="ck-card">
                    <div class="ck-card-head">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        Destino do Envio
                    </div>
                    <div class="ck-card-body">
                        <div class="ck-address-grid">
                            <div>
                                <div class="ck-field">
                                    <span class="k">Comprador</span>
                                    <span class="v"><?php echo htmlspecialchars($nomeSessao, ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="ck-field">
                                    <span class="k">Recebedor no Local</span>
                                    <span class="v"><?php echo htmlspecialchars($cliente->getNome(), ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="ck-field">
                                    <span class="k">Telefone Contato</span>
                                    <span class="v"><?php echo htmlspecialchars($cliente->getTelefone(), ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="ck-field">
                                    <span class="k">Pagamento</span>
                                    <span class="v">Cartão de Crédito &middot; Final <?php echo substr($cliente->getCartaoCredito(), -4); ?></span>
                                </div>
                            </div>
                            <div>
                                <?php if ($enderecoObj) { ?>
                                    <div class="ck-field">
                                        <span class="k">Endereço</span>
                                        <span class="v">
                                            <?php echo htmlspecialchars($enderecoObj->getRua() . ', ' . $enderecoObj->getNumero(), ENT_QUOTES, 'UTF-8'); ?>
                                            <?php if (method_exists($enderecoObj, 'getComplemento') && $enderecoObj->getComplemento()) echo ' - ' . htmlspecialchars($enderecoObj->getComplemento(), ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </div>
                                    <div class="ck-field">
                                        <span class="k">Bairro / CEP</span>
                                        <span class="v">
                                            <?php echo method_exists($enderecoObj, 'getBairro') ? htmlspecialchars($enderecoObj->getBairro(), ENT_QUOTES, 'UTF-8') : ''; ?>
                                            &middot;
                                            <?php echo method_exists($enderecoObj, 'getCep') ? htmlspecialchars($enderecoObj->getCep(), ENT_QUOTES, 'UTF-8') : ''; ?>
                                        </span>
                                    </div>
                                    <div class="ck-field">
                                        <span class="k">Cidade / UF</span>
                                        <span class="v">
                                            <?php echo method_exists($enderecoObj, 'getCidade') ? htmlspecialchars($enderecoObj->getCidade(), ENT_QUOTES, 'UTF-8') : ''; ?><?php echo method_exists($enderecoObj, 'getEstado') ? '/' . htmlspecialchars($enderecoObj->getEstado(), ENT_QUOTES, 'UTF-8') : ''; ?>
                                        </span>
                                    </div>
                                <?php } else { ?>
                                    <p style="color: var(--ck-danger); font-size: 13.5px;">Detalhes do endereço não encontrados.</p>
                                <?php } ?>
                            </div>
                        </div>

                        <a href="/Pages/Addresses/edit.php?id=<?php echo $enderecoObj ? $enderecoObj->getId() : ''; ?>&checkout=1" class="ck-btn ck-btn-outline" style="margin-top: 8px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>
                            Editar Meu Endereço
                        </a>
                    </div>
                </div>

                <div class="ck-actions-row">
                    <a href="/Pages/Products/cart.php" class="ck-btn ck-btn-ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        Voltar ao Carrinho
                    </a>

                    <form method="POST" action="/Service/Products/checkout_action.php" style="display: inline;">
                        <input type="hidden" name="nome" value="<?php echo htmlspecialchars($nomeSessao, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($cliente->getEmail(), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="telefone" value="<?php echo htmlspecialchars($cliente->getTelefone(), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="cartao_credito" value="<?php echo htmlspecialchars($cliente->getCartaoCredito(), ENT_QUOTES, 'UTF-8'); ?>">

                        <button type="submit" class="ck-btn ck-btn-success ck-btn-lg">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            Confirmar e Gerar Pedido
                        </button>
                    </form>
                </div>

            <?php } else { ?>
                <div class="ck-note ck-note-info">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    <div>
                        <h4>Endereço de Entrega Pendente</h4>
                        <p>Você precisa registrar para onde enviaremos a mercadoria antes de pagar.</p>
                    </div>
                </div>

                <div class="ck-actions-row">
                    <a href="/Pages/Addresses/create.php?checkout=1" class="ck-btn ck-btn-primary ck-btn-lg">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                        Cadastrar Novo Endereço de Entrega
                    </a>
                    <a href="/Pages/Products/cart.php" class="ck-btn ck-btn-ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        Voltar ao Carrinho
                    </a>
                </div>
            <?php } ?>
        </div>

        <div class="col-md-4">
            <div class="ck-summary-card">
                <div class="ck-summary-head">Resumo do Pedido</div>
                <div class="ck-summary-body">
                    <div class="ck-summary-row">
                        <span class="label">Produtos</span>
                        <span class="value"><?php echo count($cartItems); ?></span>
                    </div>
                    <div class="ck-summary-row">
                        <span class="label">Unidades</span>
                        <span class="value"><?php echo array_sum(array_column($cartItems, 'quantidade')); ?></span>
                    </div>
                    <div class="ck-summary-row total-row">
                        <span class="label">Total</span>
                        <span class="value">R$ <?php echo number_format($cartTotal, 2, ',', '.'); ?></span>
                    </div>

                    <?php if ($cliente) { ?>
                        <div class="ck-summary-status ok">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
                            <span><strong>Tudo pronto!</strong> O botão de finalização está liberado.</span>
                        </div>
                    <?php } else { ?>
                        <div class="ck-summary-status pending">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><path d="M12 9v4M12 17h.01"/></svg>
                            <span><strong>Pendência:</strong> cadastre um endereço de entrega para liberar o pedido.</span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>