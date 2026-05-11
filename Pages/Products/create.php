<?php

require_once __DIR__ . '/../../Service/Auth/session.php';
require_internal_user();

$page_title = 'Novo Produto';
include_once __DIR__ . '/../Common/layout_header.php';

require_once __DIR__ . '/../../config/app.php';
$fornecedores = $factory->getFornecedorDao()->buscaTodos();
?>
<section class="row">
    <div class="col-md-8">
        <form action="/Service/Products/create_action.php" method="post" class="panel panel-default" style="padding:20px;">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="fornecedor_id">Fornecedor</label>
                <select id="fornecedor_id" name="fornecedor_id" class="form-control" required>
                    <option value="">Selecione um fornecedor</option>
                    <?php foreach ($fornecedores as $fornecedor) { ?>
                        <option value="<?php echo (int) $fornecedor->getId(); ?>">
                            <?php echo htmlspecialchars($fornecedor->getNome(), ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="quantidade">Quantidade inicial</label>
                    <input type="number" id="quantidade" name="quantidade" class="form-control" min="0" value="0" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="preco">Preço inicial</label>
                    <input type="text" id="preco" name="preco" class="form-control" value="0,00" required>
                </div>
            </div>
            <p class="help-block">O estoque será criado junto com o produto.</p>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="/Pages/Products/list.php" class="btn btn-default">Cancelar</a>
        </form>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
