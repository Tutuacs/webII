<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';
require_once __DIR__ . '/CartService.php';

ensure_session_started();

header('Content-Type: application/json');

$produtoId = isset($_POST['produto_id']) ? (int)$_POST['produto_id'] : 0;
$quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 0;

if ($produtoId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID do produto inválido.',
    ]);
    exit;
}

$cartService = new CartService($factory);
$result = $cartService->updateCartQuantity($produtoId, $quantidade);

// Adiciona o novo total à resposta
$result['novoTotal'] = $cartService->getCartTotal();

http_response_code($result['success'] ? 200 : 400);
echo json_encode($result);
