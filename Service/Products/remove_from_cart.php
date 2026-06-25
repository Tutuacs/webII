<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';
require_once __DIR__ . '/../Cart/CartService.php';

ensure_session_started();

$produtoId = isset($_GET['produto_id']) ? (int)$_GET['produto_id'] : 0;

if ($produtoId <= 0) {
    set_flash_message('warning', 'ID do produto inválido.');
    header('Location: /Pages/Products/cart.php');
    exit;
}

$cartService = new CartService($factory);
$cartService->removeFromCart($produtoId);

set_flash_message('success', 'Produto removido do carrinho com sucesso!');
header('Location: /Pages/Products/cart.php');
exit;
