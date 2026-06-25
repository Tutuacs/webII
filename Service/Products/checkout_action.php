<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Auth/session.php';
require_once __DIR__ . '/../Cart/CartService.php';

ensure_session_started();

// 1. Valida se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    set_flash_message('danger', 'Você precisa estar logado para finalizar a compra.');
    header('Location: /Pages/Login/index.php?redirect=/Pages/Products/checkout.php');
    exit;
}

// 2. Valida se o cliente está vinculado
$clienteId = isset($_SESSION['cliente_id']) ? (int)$_SESSION['cliente_id'] : null;
if (!$clienteId) {
    set_flash_message('danger', 'Seu perfil de cliente não foi encontrado. Complete seu cadastro.');
    header('Location: /Pages/Addresses/create.php?checkout=1');
    exit;
}

// 3. Valida o carrinho
$cartService = new CartService($factory);
$validation = $cartService->validateCart();
if (!$validation['success']) {
    set_flash_message('danger', $validation['message']);
    header('Location: /Pages/Products/cart.php');
    exit;
}

$cartItems = $cartService->getCartItems();

try {

    $enderecoEntrega = $factory->getEnderecoDao()->buscaPorId($clienteId);
    if (!$enderecoEntrega) {
        throw new Exception('Dados de entrega não encontrados no banco de dados.');
    }

    $factory->getConnection()->beginTransaction();

    // 5. Cria um novo pedido
    $pedido = new Pedido();
    $pedido->setClienteId($clienteId);
    $pedido->setDataPedido(date('Y-m-d H:i:s'));
    $pedido->setDataEntrega(date('Y-m-d', strtotime('+7 days')));
    $pedido->setSituacao('NOVO');

    // Salva o pedido
    $pedidoId = $factory->getPedidoDao()->insere($pedido);
    if (!$pedidoId) {
        throw new Exception('Erro ao criar o pedido no banco de dados.');
    }

    // 6. Cria os itens de pedido e decrementa o estoque
    foreach ($cartItems as $item) {
        $produto = $item['produto'];
        $estoque = $item['estoque'];
        $quantidade = $item['quantidade'];
        $preco = $estoque ? $estoque->getPreco() : 0;

        // Double-check de segurança de concorrência antes de baixar
        if (!$estoque || $estoque->getQuantidade() < $quantidade) {
            throw new Exception("O produto '{$produto->getNome()}' não possui estoque suficiente para esta operação.");
        }

        // Cria o item de pedido
        $itemPedido = new ItemPedido();
        $itemPedido->setPedidoId($pedidoId);
        $itemPedido->setProdutoId($produto->getId());
        $itemPedido->setQuantidade($quantidade);
        $itemPedido->setPreco($preco);

        $factory->getItemPedidoDao()->insere($itemPedido);

        // REGRA 3: Decrementa o estoque usando o método correto mapeado
        $novaQuantidade = $estoque->getQuantidade() - $quantidade;
        $estoque->setQuantidade($novaQuantidade);
        $factory->getEstoqueDao()->altera($estoque);
    }

    // Grava  no banco
    $factory->getConnection()->commit();

    // 7. Limpa o carrinho
    $cartService->clearCart();

    // 8. Redireciona para página de sucesso
    set_flash_message('success', 'Pedido criado com sucesso! Você pode acompanhar seu pedido em "Meus Pedidos".');
    header('Location: /Pages/Products/order_success.php?pedido_id=' . $pedidoId);
    exit;

} catch (Throwable $e) {
    //Cancela e desfaz qualquer alteração feita no banco nesta tentativa
    if (isset($factory) && $factory->getConnection()->inTransaction()) {
        $factory->getConnection()->rollBack();
    }
    
    error_log('Erro no checkout: ' . $e->getMessage());
    
    set_flash_message('danger', 'Erro ao processar o pedido: ' . $e->getMessage());
    header('Location: /Pages/Products/checkout.php');
    exit;
}