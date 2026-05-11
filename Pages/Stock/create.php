<?php

require_once __DIR__ . '/../../Service/Auth/session.php';
require_internal_user();

require_once __DIR__ . '/../../config/app.php';
$produtos = $factory->getProdutoDao()->buscaTodos();

$page_title = 'Novo Estoque';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section class="row">
    <div class="col-md-8">
        <form action="/Service/Stock/create_action.php" method="post" class="panel panel-default" style="padding:20px;">
            <div class="form-group">
                <label for="produto_id">Produto</label>
                <select id="produto_id" name="produto_id" class="form-control" required>
                    <option value="">Selecione um produto</option>
                    <?php foreach ($produtos as $produto) { ?>
                        <option value="<?php echo (int) $produto->getId(); ?>">
                            <?php echo htmlspecialchars($produto->getNome(), ENT_QUOTES, 'UTF-8'); ?><?php echo $produto->getEstoqueId() ? ' (estoque existente)' : ''; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantidade">Quantidade</label>
                <input type="number" id="quantidade" name="quantidade" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label for="preco">Preço</label>
                <input type="text" id="preco" name="preco" class="form-control" placeholder="0,00" required>
            </div>
            <p class="help-block"><strong>Nota:</strong> Cada produto pode ter múltiplos estoques. Quando criar o primeiro estoque para um produto, ele será automaticamente selecionado como "ativo".</p>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="/Pages/Stock/list.php" class="btn btn-default">Cancelar</a>
        </form>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
