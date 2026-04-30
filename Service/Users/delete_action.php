<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
ensure_session_started();
$currentUserId = isset($_SESSION['id_usuario']) ? (int) $_SESSION['id_usuario'] : 0;

if ($id === $currentUserId) {
	set_flash_message('warning', 'Você não pode excluir seu próprio usuário.');
	header('Location: /Pages/Users/list.php');
	exit;
}

safe_remove_with_flash(
	function () use ($factory, $id) {
		$factory->getUsuarioDao()->removePorId($id);
	},
	'/Pages/Users/list.php',
	'usuário'
);
