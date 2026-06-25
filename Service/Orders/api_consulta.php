<?php

// 1. Carrega as configurações
require_once __DIR__ . '/../../config/app.php';

// 2. Define os cabeçalhos obrigatórios para uma API REST
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET');

// 3. Valida se a requisição é do tipo GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

// 4. Captura os parâmetros da URL
$numero = isset($_GET['numero']) && $_GET['numero'] !== '' ? (int) $_GET['numero'] : null;
$nome_cliente = isset($_GET['cliente']) ? trim($_GET['cliente']) : null;

try {
    $conn = $factory->getConnection();
    
    // 5. Monta a query principal
    $sql = "SELECT p.id as pedido_numero, p.data_pedido, p.data_entrega, p.situacao, 
                   c.nome as cliente_nome, c.email as cliente_email
            FROM pedido p
            INNER JOIN cliente c ON p.cliente_id = c.id
            WHERE 1=1";
            
    $params = [];
    
    // 6. Adiciona os filtros se existirem
    if ($numero) {
        $sql .= " AND p.id = :numero";
        $params[':numero'] = $numero;
    } elseif ($nome_cliente) {
        $sql .= " AND c.nome LIKE :cliente";
        $params[':cliente'] = '%' . $nome_cliente . '%';
    }

    // 7. Ordena do mais recente para o mais antigo e limita a 50 (para não pesar o banco)
    $sql .= " ORDER BY p.id DESC LIMIT 50";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 8. Se não encontrar nada, retorna 404 de forma amigável
    if (empty($pedidos)) {
        http_response_code(404);
        echo json_encode(['mensagem' => 'Nenhum pedido encontrado.']);
        exit;
    }

    // 9. Busca os itens de cada pedido
    foreach ($pedidos as &$pedido) {
        $sqlItens = "SELECT produto_id, quantidade, preco FROM item_pedido WHERE pedido_id = :pedido_id";
        $stmtItens = $conn->prepare($sqlItens);
        $stmtItens->execute([':pedido_id' => $pedido['pedido_numero']]);
        
        $pedido['itens'] = $stmtItens->fetchAll(PDO::FETCH_ASSOC);
    }

    // 10. Retorna sucesso
    http_response_code(200);
    echo json_encode([
        'sucesso' => true,
        'quantidade' => count($pedidos),
        'dados' => $pedidos
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno no servidor.', 'detalhe' => $e->getMessage()]);
}