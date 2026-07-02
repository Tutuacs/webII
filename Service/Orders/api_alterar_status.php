<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

// Garante que o cabeçalho responda JSON para o JavaScript
header('Content-Type: application/json');

// 1. IMPORTANTE: Inicializa e valida o usuário com as funções nativas do seu sistema
try {
    ensure_session_started();
    
    // Se o usuário não for interno/admin, barramos aqui
    if (!function_exists('is_internal_user') || !is_internal_user()) {
        // Fallback caso is_internal_user() não seja uma função puramente booleana:
        // Avaliamos direto se existe a role na sessão.
        if (!isset($_SESSION['role_usuario'])) {
            echo json_encode(['sucesso' => false, 'erro' => 'Não autorizado. Usuário precisa ser interno.']);
            exit;
        }
    }
} catch (Throwable $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Falha na validação da sessão de administrador.']);
    exit;
}

// 2. Recebe e sanitiza as variáveis vindas do POST do AJAX
$pedidoId = isset($_POST['pedido_id']) ? (int)$_POST['pedido_id'] : 0;
$situacao = isset($_POST['situacao']) ? trim($_POST['situacao']) : '';

// Lista de ENUMs permitidos para evitar injeção de valores inválidos
$statusPermitidos = [
    'PENDENTE',
    'PROCESSANDO',
    'ENVIADO',
    'ENTREGUE',
    'CANCELADO'
];

if ($pedidoId > 0 && in_array($situacao, $statusPermitidos)) {
    try {
        $conn = $factory->getConnection();
        
        // Atualiza o status do pedido no banco de dados
        $stmt = $conn->prepare("UPDATE pedido SET situacao = :situacao WHERE id = :id");
        $resultado = $stmt->execute([
            ':situacao' => $situacao, 
            ':id'       => $pedidoId
        ]);
        
        if ($resultado) {
            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível atualizar o registro no banco.']);
        }
    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'erro' => 'Erro no banco de dados: ' . $e->getMessage()]);
    }
} else {
    echo json_encode([
        'sucesso' => false, 
        'erro' => 'Dados inválidos. O status deve ser NOVO, ENTREGUE ou CANCELADO.'
    ]);
}
exit;