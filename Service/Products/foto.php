<?php
/**
 * Service/Products/foto.php
 * Serve a foto de um produto armazenada como BLOB no banco.
 * Uso: <img src="/Service/Products/foto.php?id=123">
 */

require_once __DIR__ . '/../../config/app.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    exit;
}

$produto = $factory->getProdutoDao()->buscaPorId($id);

if (!$produto || !$produto->getFoto()) {
    http_response_code(404);
    exit;
}

$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->buffer($produto->getFoto());

if (!$mimeType || strpos($mimeType, 'image/') !== 0) {
    http_response_code(415);
    exit;
}

header('Content-Type: ' . $mimeType);
header('Cache-Control: public, max-age=86400');
echo $produto->getFoto();
exit;