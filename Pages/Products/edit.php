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
$estoqueAtivo = $produto->getEstoqueId() ? $factory->getEstoqueDao()->buscaPorId($produto->getEstoqueId()) : null;
$todosEstoques = $factory->getEstoqueDao()->buscaPorProdutoId($produto->getId());
?>
<section class="row">
    <div class="col-md-8">
        <form action="/Service/Products/update_action.php" method="post" enctype="multipart/form-data" class="panel panel-default" style="padding:20px;">
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
                <label>Foto do produto</label>
                <?php if ($produto->getFoto()) { ?>
                    <div style="margin-bottom:10px;">
                        <img src="/Service/Products/foto.php?id=<?php echo (int) $produto->getId(); ?>"
                             alt="Foto atual"
                             style="max-height:160px; max-width:100%; border:1px solid #ddd; border-radius:4px; padding:4px;">
                        <p class="help-block">Foto atual. Envie uma nova para substituir.</p>
                    </div>
                    <input type="hidden" name="manter_foto" value="1">
                <?php } ?>
                <input type="file" id="foto" name="foto" class="form-control" accept="image/*">
                <p class="help-block">Opcional. Formatos aceitos: JPG, PNG, GIF, WebP.</p>
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
            
            <hr>
            <h4>Estoque Selecionado (Ativo)</h4>
            
            <?php if (!empty($todosEstoques)) { ?>
                <div class="form-group">
                    <label for="estoque_id">Escolher estoque ativo</label>
                    <select id="estoque_id" name="estoque_id" class="form-control" required>
                        <option value="">Nenhum estoque selecionado</option>
                        <?php foreach ($todosEstoques as $e) { ?>
                            <option value="<?php echo (int) $e->getId(); ?>" <?php echo $estoqueAtivo && (int) $e->getId() === (int) $estoqueAtivo->getId() ? 'selected' : ''; ?>>
                                Estoque #<?php echo (int) $e->getId(); ?> - Qtd: <?php echo (int) $e->getQuantidade(); ?> - R$ <?php echo number_format($e->getPreco(), 2, ',', '.'); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>
            
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="quantidade">Quantidade do estoque ativo</label>
                    <input type="number" id="quantidade" name="quantidade" class="form-control" min="0" value="<?php echo $estoqueAtivo ? (int) $estoqueAtivo->getQuantidade() : 0; ?>" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="preco">Preço do estoque ativo</label>
                    <input type="text" id="preco" name="preco" class="form-control" value="<?php echo $estoqueAtivo ? htmlspecialchars(number_format($estoqueAtivo->getPreco(), 2, ',', '.'), ENT_QUOTES, 'UTF-8') : '0,00'; ?>" required>
                </div>
            </div>
            <p class="help-block"><strong>Nota:</strong> Quantidade e preço acima referem-se ao estoque selecionado. Este produto pode ter outros estoques alternativos, mas apenas um é considerado "ativo".</p>
            
            <?php if (count($todosEstoques) > 1) { ?>
                <p class="help-block">
                    <strong>Outros estoques disponíveis:</strong><br>
                    <?php foreach ($todosEstoques as $e) { ?>
                        <?php if (!$estoqueAtivo || (int) $e->getId() !== (int) $estoqueAtivo->getId()) { ?>
                            Estoque #<?php echo (int) $e->getId(); ?> - Qtd: <?php echo (int) $e->getQuantidade(); ?> - R$ <?php echo number_format($e->getPreco(), 2, ',', '.'); ?><br>
                        <?php } ?>
                    <?php } ?>
                </p>
            <?php } ?>
            
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="/Pages/Products/list.php" class="btn btn-default">Cancelar</a>
        </form>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>