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
    // Puxa a conexão PDO que já foi criada no seu config/app.php
    $conn = $factory->getConnection();
    
    // Passa a conexão obrigatoriamente para o construtor da ClasseDAO
    $itemPedidoDAO = new ItemPedidoDAO($conn); 
    
    // Busca os itens do banco
    $itens = $itemPedidoDAO->buscaPorPedidoId($pedido_id);
    
    $dados = [];
    if (!empty($itens)) {
        foreach ($itens as $item) {
            $dados[] = [
                'produto_id' => $item->getProdutoId(),
                'quantidade' => $item->getQuantidade(),
                'preco'      => $item->getPreco()
            ];
        }
    }

    // Devolve o JSON bonitinho para o JavaScript montar o modal
    echo json_encode(['itens' => $dados]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno no servidor: ' . $e->getMessage()]);
}