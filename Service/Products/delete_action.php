<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';

require_internal_user();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$factory->getProdutoDao()->removePorId($id);

header('Location: /Pages/Products/list.php');
exit;
