<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
$telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$enderecoId = isset($_POST['endereco_id']) ? trim($_POST['endereco_id']) : null;

$fornecedor = new Fornecedor($id, $nome, $descricao, $telefone, $email, $enderecoId);
$factory->getFornecedorDao()->altera($fornecedor);

header('Location: /Pages/Suppliers/list.php');
exit;
