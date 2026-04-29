<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$quantidade = isset($_POST['quantidade']) ? (int) $_POST['quantidade'] : 0;
$preco = isset($_POST['preco']) ? (float) str_replace(',', '.', $_POST['preco']) : 0;

$estoque = new Estoque(null, $quantidade, $preco);
$factory->getEstoqueDao()->insere($estoque);

header('Location: /Pages/Stock/list.php');
exit;
