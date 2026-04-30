<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
safe_remove_with_flash(
	function () use ($factory, $id) {
		$factory->getEstoqueDao()->removePorId($id);
	},
	'/Pages/Stock/list.php',
	'estoque'
);
