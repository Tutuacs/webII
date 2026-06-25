<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$veioDoCheckout = isset($_POST['checkout']) || isset($_GET['checkout']);

try {
    $rua         = isset($_POST['rua'])         ? trim($_POST['rua'])         : '';
    $numero      = isset($_POST['numero'])      ? trim($_POST['numero'])      : '';
    $complemento = isset($_POST['complemento']) ? trim($_POST['complemento']) : '';
    $bairro      = isset($_POST['bairro'])      ? trim($_POST['bairro'])      : '';
    $cep         = isset($_POST['cep'])         ? trim($_POST['cep'])         : '';
    $cidade      = isset($_POST['cidade'])      ? trim($_POST['cidade'])      : '';
    $estado      = isset($_POST['estado'])      ? trim($_POST['estado'])      : '';

    $endereco = new Endereco(null, $rua, $numero, $complemento, $bairro, $cep, $cidade, $estado);
    $enderecoId = $factory->getEnderecoDao()->insere($endereco);

    $nome           = isset($_POST['nome'])           ? trim($_POST['nome'])           : '';
    $telefone       = isset($_POST['telefone'])       ? trim($_POST['telefone'])       : '';
    $email          = isset($_POST['email'])          ? trim($_POST['email'])          : '';
    $cartao_credito = isset($_POST['cartao_credito']) ? trim($_POST['cartao_credito']) : '';

    $stmt = $factory->getConnection()->prepare("INSERT INTO cliente (nome, telefone, email, cartao_credito, endereco_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nome, $telefone, $email, $cartao_credito, $enderecoId]);
    $clienteId = $factory->getConnection()->lastInsertId();

    if ($veioDoCheckout && $clienteId) {
        $_SESSION['cliente_id'] = (int)$clienteId;
        
        set_flash_message('success', 'Perfil e endereço registados com sucesso!');
        header('Location: /Pages/Products/checkout.php');
        exit;
    }

    set_flash_message('success', 'Cliente cadastrado com sucesso!');
    header('Location: /Pages/Addresses/list.php');
    exit;

} catch (Throwable $e) {
    error_log('Erro ao cadastrar cliente/endereço: ' . $e->getMessage());
    set_flash_message('danger', 'Erro no registo: ' . $e->getMessage());
    
    if ($veioDoCheckout) {
        header('Location: /Pages/Addresses/create.php?checkout=1');
    } else {
        header('Location: /Pages/Addresses/list.php');
    }
    exit;
}