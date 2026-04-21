<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$login = isset($_POST['login']) ? trim($_POST['login']) : '';
$senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : 'INTERNO';

$usuario = new Usuario(null, $login, $senha, $nome, $role);
$factory->getUsuarioDao()->insere($usuario);

header('Location: /Pages/Users/list.php');
exit;
