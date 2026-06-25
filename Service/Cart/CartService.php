<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/app.php';

class CartService
{
    private $daoFactory;

    public function __construct($daoFactory)
    {
        $this->daoFactory = $daoFactory;
    }

    /**
     * Obtém os itens do carrinho com informações completas do produto
     * @return array Array de produtos com informações de quantidade e estoque
     */
    public function getCartItems(): array
    {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            return [];
        }

        $items = [];
        
        foreach ($_SESSION['cart'] as $produtoId => $quantidade) {
            try {
                $produto = $this->daoFactory->getProdutoDao()->buscaPorId((int)$produtoId);
                if ($produto) {
                    // Obtém informações de estoque
                    $estoqueId = $produto->getEstoqueId();
                    $estoque = $this->daoFactory->getEstoqueDao()->buscaPorId($estoqueId);
                    
                    $items[] = [
                        'produto' => $produto,
                        'estoque' => $estoque,
                        'quantidade' => $quantidade,
                        'subtotal' => $quantidade * ($estoque ? $estoque->getPreco() : 0),
                    ];
                }
            } catch (Throwable $e) {
                continue;
            }
        }

        return $items;
    }

    /**
     * Calcula o valor total do carrinho
     * @return float Valor total em reais
     */
    public function getCartTotal(): float
    {
        $items = $this->getCartItems();
        $total = 0;

        foreach ($items as $item) {
            $total += $item['subtotal'];
        }

        return $total;
    }

    /**
     * Valida o carrinho antes do checkout
     * @return array Array com 'success' => bool e 'message' => string
     */
    public function validateCart(): array
    {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return [
                'success' => false,
                'message' => 'Seu carrinho está vazio.',
            ];
        }

        $items = $this->getCartItems();

        // Valida cada produto
        foreach ($items as $item) {
            $produto = $item['produto'];
            $estoque = $item['estoque'];
            $quantidadeSolicitada = $item['quantidade'];

            // Regra 1: Estoque Zero
            if (!$estoque || $estoque->getQuantidade() <= 0) {
                return [
                    'success' => false,
                    'message' => "O produto '{$produto->getNome()}' está indisponível no momento.",
                ];
            }

            // Regra 2: Validação de Quantidade
            if ($quantidadeSolicitada > $estoque->getQuantidade()) {
                return [
                    'success' => false,
                    'message' => "Você tentou comprar {$quantidadeSolicitada} unidades de '{$produto->getNome()}', mas apenas {$estoque->getQuantidade()} estão disponíveis.",
                ];
            }
        }

        return [
            'success' => true,
            'message' => 'Carrinho válido.',
        ];
    }

    /**
     * Atualiza a quantidade de um produto no carrinho
     * @param int $produtoId ID do produto
     * @param int $novaQuantidade Nova quantidade desejada
     * @return array Array com 'success' => bool e 'message' => string
     */
    public function updateCartQuantity(int $produtoId, int $novaQuantidade): array
    {
        if (!isset($_SESSION['cart'][$produtoId])) {
            return [
                'success' => false,
                'message' => 'Produto não encontrado no carrinho.',
            ];
        }

        if ($novaQuantidade < 0) {
            return [
                'success' => false,
                'message' => 'A quantidade não pode ser negativa.',
            ];
        }

        // Se quantidade é 0, remove do carrinho
        if ($novaQuantidade === 0) {
            unset($_SESSION['cart'][$produtoId]);
            return [
                'success' => true,
                'message' => 'Produto removido do carrinho.',
                'novoTotal' => $this->getCartTotal(),
            ];
        }

        // Valida disponibilidade
        try {
            $produto = $this->daoFactory->getProdutoDao()->buscaPorId($produtoId);
            if (!$produto) {
                return [
                    'success' => false,
                    'message' => 'Produto não encontrado no banco de dados.',
                ];
            }

            $estoque = $this->daoFactory->getEstoqueDao()->buscaPorId($produto->getEstoqueId());
            if (!$estoque || $estoque->getQuantidade() <= 0) {
                return [
                    'success' => false,
                    'message' => "O produto '{$produto->getNome()}' está indisponível.",
                ];
            }

            if ($novaQuantidade > $estoque->getQuantidade()) {
                return [
                    'success' => false,
                    'message' => "Apenas {$estoque->getQuantidade()} unidades disponíveis.",
                    'maxQuantidade' => $estoque->getQuantidade(),
                ];
            }

            $_SESSION['cart'][$produtoId] = $novaQuantidade;
            return [
                'success' => true,
                'message' => 'Quantidade updated.',
                'novoTotal' => $this->getCartTotal(),
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Erro ao validar produto: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Remove um produto do carrinho
     * @param int $produtoId ID do produto
     */
    public function removeFromCart(int $produtoId): void
    {
        if (isset($_SESSION['cart'][$produtoId])) {
            unset($_SESSION['cart'][$produtoId]);
        }
    }

    /**
     * Limpa o carrinho completamente
     */
    public function clearCart(): void
    {
        $_SESSION['cart'] = [];
    }

    /**
     * Obtém a quantidade de itens únicos no carrinho
     * @return int Quantidade de produtos diferentes
     */
    public function getCartItemCount(): int
    {
        return isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
    }

    /**
     * Obtém a quantidade total de unidades no carrinho
     * @return int Total de unidades
     */
    public function getCartUnitCount(): int
    {
        if (!isset($_SESSION['cart'])) {
            return 0;
        }

        return (int) array_sum($_SESSION['cart']);
    }
}