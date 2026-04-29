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

$page_title = 'Editar Estoque';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section class="row">
    <div class="col-md-8">
        <form action="/Service/Stock/update_action.php" method="post" class="panel panel-default" style="padding:20px;">
            <input type="hidden" name="id" value="<?php echo (int) $estoque->getId(); ?>">
            <div class="form-group">
                <label for="quantidade">Quantidade</label>
                <input type="number" id="quantidade" name="quantidade" class="form-control" value="<?php echo (int) $estoque->getQuantidade(); ?>" min="0" required>
            </div>
            <div class="form-group">
                <label for="preco">Preço</label>
                <input type="text" id="preco" name="preco" class="form-control" value="<?php echo number_format($estoque->getPreco(), 2, ',', '.'); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="/Pages/Stock/list.php" class="btn btn-default">Cancelar</a>
        </form>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
