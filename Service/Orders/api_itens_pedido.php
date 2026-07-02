<?php
// Arquivo: /Service/Orders/api_itens_pedido.php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../dao/ClasseDAO.php';


 require_once __DIR__ . '/../../dao/IItemPedidoDao.php'; 

require_once __DIR__ . '/../../model/ItemPedido.php';
require_once __DIR__ . '/../../dao/mysql/ItemPedidoDAO.php'; 

header('Content-Type: application/json');

$pedido_id = isset($_GET['pedido_id']) ? (int)$_GET['pedido_id'] : 0;

if ($pedido_id <= 0) {
    echo json_encode(['erro' => 'ID do pedido inválido']);
    exit;
}

try {
    $conn = $factory->getConnection();
    $itemPedidoDAO = new ItemPedidoDAO($conn); 
    
    $itens = $itemPedidoDAO->buscaItensComProdutoPorPedidoId($pedido_id);
    
    $dados = [];
    if (!empty($itens)) {
        foreach ($itens as $item) {
            $dados[] = [
                'produto_nome'      => $item['produto_nome'],      
                'produto_descricao' => $item['produto_descricao'], 
                'quantidade'        => $item['quantidade'],
                'preco'             => $item['preco'],
                'foto_base64'       => $item['foto'] ? base64_encode($item['foto']) : null
            ];
        }
    }

    echo json_encode(['itens' => $dados]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro: ' . $e->getMessage()]);
}