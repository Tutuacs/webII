<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/session.php';

ensure_session_started();

$login = isset($_POST['login']) ? trim($_POST['login']) : '';
$senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
$redirect = isset($_POST['redirect']) ? (string) $_POST['redirect'] : '/index.php';

if ($redirect === '' || $redirect[0] !== '/') {
    $redirect = '/index.php';
}

if ($login === '' || $senha === '') {
    set_flash_message('warning', 'Informe login e senha.');
    header('Location: /Pages/Login/index.php?redirect=' . urlencode($redirect));
    exit;
}

$usuario = $factory->getUsuarioDao()->buscaPorLogin($login);

if ($usuario && md5($senha) === $usuario->getSenha()) {
    $_SESSION['id_usuario'] = $usuario->getId();
    $_SESSION['nome_usuario'] = $usuario->getNome();
    $_SESSION['login_usuario'] = $usuario->getLogin();
    $_SESSION['role_usuario'] = $usuario->getRole();

    set_flash_message('success', 'Login realizado com sucesso.');

    header('Location: ' . $redirect);
    exit;
}

set_flash_message('danger', 'Login ou senha inválidos.');
header('Location: /Pages/Login/index.php?redirect=' . urlencode($redirect));
exit;
