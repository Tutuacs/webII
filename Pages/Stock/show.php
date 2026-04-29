<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$estoque = $factory->getEstoqueDao()->buscaPorId($id);

if (!$estoque) {
    header('Location: /Pages/Stock/list.php');
    exit;
}

$page_title = 'Detalhes do Estoque';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <h2>Estoque ID: <?php echo (int) $estoque->getId(); ?></h2>
    <p><strong>Quantidade:</strong> <?php echo (int) $estoque->getQuantidade(); ?></p>
    <p><strong>Preço:</strong> R$ <?php echo number_format($estoque->getPreco(), 2, ',', '.'); ?></p>
    <a href="/Pages/Stock/list.php" class="btn btn-default">Voltar</a>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
