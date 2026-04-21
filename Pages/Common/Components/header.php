<?php
if (!isset($page_title)) {
    $page_title = 'DAO3 E-commerce';
}

if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/libs/css/custom2.css?v=2">
    <link rel="stylesheet" href="/libs/css/custom.css?v=2" />
</head>
<body class="ecom-body">
