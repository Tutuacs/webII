<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$page_title = 'Produtos';
$dao = $factory->getProdutoDao();

$filtro = isset($_GET['filtro']) ? trim((string) $_GET['filtro']) : 'nome';
$busca = isset($_GET['busca']) ? trim((string) $_GET['busca']) : '';
$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

if ($q !== '' && $busca === '') {
    $busca = $q;
    $filtro = ctype_digit($q) ? 'codigo' : 'nome';
}

if ($busca !== '') {
    if ($filtro === 'codigo') {
        $produtos = $dao->buscaPorId((int) $busca);
        $produtos = $produtos ? [$produtos] : [];
    } else {
        $produtos = $dao->buscaPorNome($busca);
    }
} else {
    $produtos = $dao->buscaTodos();
}

include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <div class="clearfix" style="margin-bottom: 15px;">
        <a href="/Pages/Products/create.php" class="btn btn-primary">Novo produto</a>
    </div>

    <form method="get" class="form-inline" style="margin-bottom: 20px;">
        <div class="form-group">
            <select name="filtro" class="form-control">
                <option value="nome" <?php echo $filtro === 'codigo' ? '' : 'selected'; ?>>Nome</option>
                <option value="codigo" <?php echo $filtro === 'codigo' ? 'selected' : ''; ?>>Código</option>
            </select>
        </div>
        <div class="form-group">
            <input type="text" name="busca" value="<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Pesquisar produto">
        </div>
        <button type="submit" class="btn btn-default">Buscar</button>
        <a href="/Pages/Products/list.php" class="btn btn-link">Limpar</a>
    </form>

    <?php if ($produtos) { ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Fornecedor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($produtos as $produto) {
                    $fornecedor = $factory->getFornecedorDao()->buscaPorId($produto->getFornecedorId());
                ?>
                    <tr>
                        <td><?php echo (int) $produto->getId(); ?></td>
                        <td><?php echo htmlspecialchars($produto->getNome(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(substr($produto->getDescricao(), 0, 50), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($fornecedor ? $fornecedor->getNome() : 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="/Pages/Products/show.php?id=<?php echo (int) $produto->getId(); ?>" class="btn btn-info btn-xs">Ver</a>
                            <a href="/Pages/Products/edit.php?id=<?php echo (int) $produto->getId(); ?>" class="btn btn-primary btn-xs">Editar</a>
                            <a href="/Service/Products/delete_action.php?id=<?php echo (int) $produto->getId(); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning">Nenhum produto encontrado.</div>
    <?php } ?>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
