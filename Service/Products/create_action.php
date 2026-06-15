<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$nome         = isset($_POST['nome'])         ? trim($_POST['nome'])                            : '';
$descricao    = isset($_POST['descricao'])    ? trim($_POST['descricao'])                       : '';
$fornecedor_id = isset($_POST['fornecedor_id']) ? (int) $_POST['fornecedor_id']                : 0;
$quantidade   = isset($_POST['quantidade'])   ? (int) $_POST['quantidade']                     : 0;
$preco        = isset($_POST['preco'])        ? (float) str_replace(',', '.', $_POST['preco']) : 0;

// Processa upload da foto
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($_FILES['foto']['tmp_name']);

    if (in_array($mimeType, $tiposPermitidos)) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
    }
}

$produto = new Produto(null, $nome, $descricao, $foto, $fornecedor_id, null);
$estoque = new Estoque(null, $quantidade, $preco, null);

(new ProdutoService($factory))->salvarComEstoque($produto, $estoque);

header('Location: /Pages/Products/list.php');
exit;