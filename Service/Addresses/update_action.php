<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$id          = isset($_POST['id'])          ? (int)$_POST['id']           : 0;
$rua         = isset($_POST['rua'])         ? trim($_POST['rua'])         : '';
$numero      = isset($_POST['numero'])      ? trim($_POST['numero'])      : '';
$complemento = isset($_POST['complemento']) ? trim($_POST['complemento']) : '';
$bairro      = isset($_POST['bairro'])      ? trim($_POST['bairro'])      : '';
$cep         = isset($_POST['cep'])         ? trim($_POST['cep'])         : '';
$cidade      = isset($_POST['cidade'])      ? trim($_POST['cidade'])      : '';
$estado      = isset($_POST['estado'])      ? trim($_POST['estado'])      : '';

$endereco = new Endereco($id, $rua, $numero, $complemento, $bairro, $cep, $cidade, $estado);

try {
    (new EnderecoService($factory))->atualizar($endereco);
    set_flash_message('success', 'Endereço atualizado com sucesso.');
} catch (PDOException $e) {
    error_log('Erro ao atualizar: ' . $e->getMessage());
    set_flash_message('danger', 'Não foi possível atualizar este endereço.');
}

if (isset($_POST['checkout']) && $_POST['checkout'] == '1') {
    header('Location: /Pages/Products/checkout.php');
} else {
    header('Location: /Pages/Addresses/list.php');
}
exit;