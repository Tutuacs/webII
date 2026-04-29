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

$page_title = 'Editar Produto';
include_once __DIR__ . '/../Common/layout_header.php';

$fornecedores = $factory->getFornecedorDao()->buscaTodos();
$estoques = $factory->getEstoqueDao()->buscaTodos();
?>
<section class="row">
    <div class="col-md-8">
        <form action="/Service/Products/update_action.php" method="post" class="panel panel-default" style="padding:20px;">
            <input type="hidden" name="id" value="<?php echo (int) $produto->getId(); ?>">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" class="form-control" value="<?php echo htmlspecialchars($produto->getNome(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="4"><?php echo htmlspecialchars($produto->getDescricao(), ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="form-group">
                <label for="fornecedor_id">Fornecedor</label>
                <select id="fornecedor_id" name="fornecedor_id" class="form-control" required>
                    <option value="">Selecione um fornecedor</option>
                    <?php foreach ($fornecedores as $fornecedor) { ?>
                        <option value="<?php echo (int) $fornecedor->getId(); ?>" <?php echo (int) $fornecedor->getId() === (int) $produto->getFornecedorId() ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($fornecedor->getNome(), ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="estoque_id">Estoque</label>
                <select id="estoque_id" name="estoque_id" class="form-control" required>
                    <option value="">Selecione um estoque</option>
                    <?php foreach ($estoques as $estoque) { ?>
                        <option value="<?php echo (int) $estoque->getId(); ?>" <?php echo (int) $estoque->getId() === (int) $produto->getEstoqueId() ? 'selected' : ''; ?>>
                            ID: <?php echo (int) $estoque->getId(); ?> - Qtd: <?php echo (int) $estoque->getQuantidade(); ?> - R$ <?php echo number_format($estoque->getPreco(), 2, ',', '.'); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="/Pages/Products/list.php" class="btn btn-default">Cancelar</a>
        </form>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
