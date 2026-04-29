<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$quantidade = isset($_POST['quantidade']) ? (int) $_POST['quantidade'] : 0;
$preco = isset($_POST['preco']) ? (float) str_replace(',', '.', $_POST['preco']) : 0;

$estoque = new Estoque($id, $quantidade, $preco);
$factory->getEstoqueDao()->altera($estoque);

header('Location: /Pages/Stock/list.php');
exit;
