<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$produto_id = isset($_POST['produto_id']) ? (int) $_POST['produto_id'] : 0;
$quantidadeInformada = array_key_exists('quantidade', $_POST);
$precoInformado = array_key_exists('preco', $_POST);
$quantidade = $quantidadeInformada ? (int) $_POST['quantidade'] : 0;
$preco = $precoInformado ? (float) str_replace(',', '.', $_POST['preco']) : 0;

$estoqueService = new EstoqueService($factory);
$estoqueAtual = $id ? $estoqueService->buscarPorId($id) : null;

if (!$produto_id && $estoqueAtual !== null) {
	$produto_id = (int) $estoqueAtual->getProdutoId();
}

if (!$quantidadeInformada && $estoqueAtual !== null) {
	$quantidade = (int) $estoqueAtual->getQuantidade();
}

if (!$precoInformado && $estoqueAtual !== null) {
	$preco = (float) $estoqueAtual->getPreco();
}

$estoque = new Estoque($id, $quantidade, $preco, $produto_id);
$estoqueService->atualizarComProduto($estoque);

header('Location: /Pages/Stock/list.php');
exit;
