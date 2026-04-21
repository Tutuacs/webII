<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

ensure_session_started();

$produtoId = isset($_GET['produto_id']) ? (int) $_GET['produto_id'] : 0;
$returnPath = isset($_GET['return']) ? (string) $_GET['return'] : '/Pages/Products/list.php';

if ($returnPath === '' || $returnPath[0] !== '/') {
    $returnPath = '/Pages/Products/list.php';
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: /Pages/Login/index.php?redirect=' . urlencode($returnPath));
    exit;
}

if ($produtoId <= 0) {
    header('Location: ' . $returnPath);
    exit;
}

$produto = $factory->getProdutoDao()->buscaPorId($produtoId);
if (!$produto) {
    header('Location: ' . $returnPath);
    exit;
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['cart'][$produtoId])) {
    $_SESSION['cart'][$produtoId] = 0;
}

$_SESSION['cart'][$produtoId]++;

$separator = strpos($returnPath, '?') !== false ? '&' : '?';
header('Location: ' . $returnPath . $separator . 'added=1');
exit;
