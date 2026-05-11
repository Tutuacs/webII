<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$produto_id = isset($_POST['produto_id']) ? (int) $_POST['produto_id'] : 0;
$quantidade = isset($_POST['quantidade']) ? (int) $_POST['quantidade'] : 0;
$preco = isset($_POST['preco']) ? (float) str_replace(',', '.', $_POST['preco']) : 0;

$estoque = new Estoque(null, $quantidade, $preco, $produto_id);
(new EstoqueService($factory))->salvarComProduto($estoque);

header('Location: /Pages/Stock/list.php');
exit;
