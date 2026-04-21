<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';
require_once __DIR__ . '/../Common/Components/product_card.php';

ensure_session_started();

$q = isset($q) ? trim((string) $q) : (isset($_GET['q']) ? trim((string) $_GET['q']) : '');
$page_title = isset($page_title) ? $page_title : 'DAO3 Shop - Produtos';

$produtos = [];
$erroProdutos = null;

try {
    if ($q !== '') {
        $produtos = $factory->getProdutoDao()->buscaPorNome($q);
    } else {
        $produtos = $factory->getProdutoDao()->buscaTodos();
    }
} catch (Throwable $e) {
    $erroProdutos = 'Não foi possível consultar os produtos no momento.';
}

$isLogged = isset($_SESSION['id_usuario']);
$returnPath = '/index.php' . ($q !== '' ? '?q=' . urlencode($q) : '');
$layout_variant = 'products';

include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <h1>Produtos em destaque</h1>
    <p>Pesquise na barra acima e clique em adicionar ao carrinho para comprar.</p>
</section>

<?php if ($q !== '') { ?>
    <p class="products-query">Resultados para: <strong><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></strong></p>
<?php } ?>

<section class="panel panel-default products-shell">
    <div class="panel-body">
        <?php if ($erroProdutos) { ?>
            <div class="alert alert-warning"><?php echo htmlspecialchars($erroProdutos, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php } elseif (!$produtos) { ?>
            <div class="alert alert-info">Nenhum produto encontrado.</div>
        <?php } else { ?>
            <div class="row">
                <?php foreach ($produtos as $produto) { ?>
                    <div class="col-sm-6 col-md-3">
                        <?php render_product_card($produto, $isLogged, $returnPath); ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
