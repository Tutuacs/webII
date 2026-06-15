<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$id            = isset($_POST['id'])            ? (int) $_POST['id']                               : 0;
$nome          = isset($_POST['nome'])          ? trim($_POST['nome'])                             : '';
$descricao     = isset($_POST['descricao'])     ? trim($_POST['descricao'])                        : '';
$fornecedor_id = isset($_POST['fornecedor_id']) ? (int) $_POST['fornecedor_id']                   : 0;
$estoque_id    = isset($_POST['estoque_id'])    ? (int) $_POST['estoque_id']                      : 0;
$quantidade    = array_key_exists('quantidade', $_POST) ? (int) $_POST['quantidade']              : null;
$preco         = array_key_exists('preco', $_POST)      ? (float) str_replace(',', '.', $_POST['preco']) : null;

$produtoService = new ProdutoService($factory);
$estoqueService = new EstoqueService($factory);

$produtoAtual = $produtoService->buscarPorId($id);
if ($produtoAtual === null) {
    header('Location: /Pages/Products/list.php');
    exit;
}

// Processa foto: nova > manter existente > null
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($_FILES['foto']['tmp_name']);

    if (in_array($mimeType, $tiposPermitidos)) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
    }
} elseif (!empty($_POST['manter_foto'])) {
    // Sem novo upload mas tinha foto — mantém a existente
    $foto = $produtoAtual->getFoto();
}

if (!$estoque_id) {
    $estoque_id = (int) $produtoAtual->getEstoqueId();
}

$estoqueAtual = $estoque_id ? $estoqueService->buscarPorId($estoque_id) : null;
$estoque = $estoqueAtual ?? new Estoque($estoque_id ?: null, 0, 0, $id);
$estoque->setProdutoId($id);

if ($quantidade !== null) {
    $estoque->setQuantidade($quantidade);
} elseif ($estoqueAtual !== null) {
    $estoque->setQuantidade($estoqueAtual->getQuantidade());
}

if ($preco !== null) {
    $estoque->setPreco($preco);
} elseif ($estoqueAtual !== null) {
    $estoque->setPreco($estoqueAtual->getPreco());
}

$produto = new Produto($id, $nome, $descricao, $foto, $fornecedor_id, $estoque_id);
$produtoService->atualizarComEstoque($produto, $estoque);

header('Location: /Pages/Products/list.php');
exit;