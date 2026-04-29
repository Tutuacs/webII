<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
$fornecedor_id = isset($_POST['fornecedor_id']) ? (int) $_POST['fornecedor_id'] : 0;
$estoque_id = isset($_POST['estoque_id']) ? (int) $_POST['estoque_id'] : 0;

$produto = new Produto(null, $nome, $descricao, null, $fornecedor_id, $estoque_id);
$factory->getProdutoDao()->insere($produto);

header('Location: /Pages/Products/list.php');
exit;
