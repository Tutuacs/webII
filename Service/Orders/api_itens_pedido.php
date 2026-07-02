<?php
// Arquivo: /Service/Orders/api_itens_pedido.php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../dao/ClasseDAO.php';
require_once __DIR__ . '/../../dao/IItemPedidoDao.php'; 
require_once __DIR__ . '/../../model/ItemPedido.php';
require_once __DIR__ . '/../../dao/mysql/ItemPedidoDAO.php'; 

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$pedido_id = isset($_GET['pedido_id']) ? (int)$_GET['pedido_id'] : 0;

if ($pedido_id <= 0) {
    http_response_code(400); 
    echo json_encode(['erro' => 'ID do pedido inválido'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

try {
    $conn = $factory->getConnection();
    $itemPedidoDAO = new ItemPedidoDAO($conn); 
    
    $itens = $itemPedidoDAO->buscaItensComProdutoPorPedidoId($pedido_id);
    
    if (empty($itens)) {
        http_response_code(404);
        echo json_encode(['erro' => 'Itens não encontrados para este pedido.'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    $dados = [];
    foreach ($itens as $item) {
        $dados[] = [
            'produto_nome'      => $item['produto_nome'],      
            'produto_descricao' => $item['produto_descricao'], 
            'quantidade'        => (int)$item['quantidade'],
            'preco'             => (float)$item['preco'],
            'foto_base64'       => $item['foto'] ? base64_encode($item['foto']) : null
        ];
    }

    echo json_encode(['itens' => $dados], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500); 
    echo json_encode([
        'erro' => 'Erro interno no servidor.',
        'detalhe' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}