<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$nome      = isset($_POST['nome'])      ? trim($_POST['nome'])      : '';
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
$telefone  = isset($_POST['telefone'])  ? trim($_POST['telefone'])  : '';
$email     = isset($_POST['email'])     ? trim($_POST['email'])     : '';

$endereco = new Endereco(null, $nome, $descricao, $telefone, $email);

try {
    (new EnderecoService($factory))->salvar($endereco);
    set_flash_message('success', 'Endereço cadastrado com sucesso.');
} catch (PDOException $e) {
    set_flash_message('danger', 'Não foi possível cadastrar este endereço.');
}

header('Location: /Pages/Addresses/list.php');
exit;