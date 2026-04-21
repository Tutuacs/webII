<?php

$page_title = 'DAO3 Shop - Produtos';
$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
include_once __DIR__ . '/Pages/Products/catalog.php';


