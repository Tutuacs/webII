<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$produto = $factory->getProdutoDao()->buscaPorId($id);

if (!$produto) {
    header('Location: /Pages/Products/list.php');
    exit;
}

$fornecedor = $factory->getFornecedorDao()->buscaPorId($produto->getFornecedorId());
$estoque = $factory->getEstoqueDao()->buscaPorId($produto->getEstoqueId());

$page_title = 'Detalhes do Produto';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <h2><?php echo htmlspecialchars($produto->getNome(), ENT_QUOTES, 'UTF-8'); ?></h2>
    <p><strong>ID:</strong> <?php echo (int) $produto->getId(); ?></p>
    <p><strong>Descrição:</strong> <?php echo htmlspecialchars($produto->getDescricao(), ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Fornecedor:</strong> <?php echo htmlspecialchars($fornecedor ? $fornecedor->getNome() : 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Estoque ID:</strong> <?php echo (int) $produto->getEstoqueId(); ?></p>
    <?php if ($estoque) { ?>
        <p><strong>Quantidade em Estoque:</strong> <?php echo (int) $estoque->getQuantidade(); ?></p>
        <p><strong>Preço:</strong> R$ <?php echo number_format($estoque->getPreco(), 2, ',', '.'); ?></p>
    <?php } ?>
    <a href="/Pages/Products/list.php" class="btn btn-default">Voltar</a>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
