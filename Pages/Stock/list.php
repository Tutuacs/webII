<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$page_title = 'Estoque';
$dao = $factory->getEstoqueDao();
$produtoDao = $factory->getProdutoDao();

$busca = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

if ($busca !== '') {
    $estoques = [];

    // Se for número, busca o estoque pelo próprio ID
    if (ctype_digit($busca)) {
        $porId = $dao->buscaPorId((int) $busca);
        if ($porId) {
            $estoques[] = $porId;
        }
    }

    // Sempre busca também pelo nome do produto vinculado
    $porNome = $dao->buscaPorNome($busca);
    foreach ($porNome as $e) {
        $jaExiste = false;
        foreach ($estoques as $existente) {
            if ($existente->getId() === $e->getId()) {
                $jaExiste = true;
                break;
            }
        }
        if (!$jaExiste) {
            $estoques[] = $e;
        }
    }
} else {
    $estoques = $dao->buscaTodos();
}

include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <div class="clearfix" style="margin-bottom: 15px;">
        <a href="/Pages/Stock/create.php" class="btn btn-primary">Novo estoque</a>
    </div>

    <form method="get" class="form-inline" style="margin-bottom: 20px;">
        <div class="form-group">
            <input type="text" name="q" value="<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Buscar por ID ou nome do produto..." style="min-width: 260px;">
        </div>
        <button type="submit" class="btn btn-default">Buscar</button>
        <?php if ($busca !== '') { ?>
            <a href="/Pages/Stock/list.php" class="btn btn-link">Limpar</a>
        <?php } ?>
    </form>

    <?php if ($busca !== '') { ?>
        <p>Resultados para: <strong><?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?></strong></p>
    <?php } ?>

    <?php if ($estoques) { ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($estoques as $estoque) {
                    $produto = $produtoDao->buscaPorId($estoque->getProdutoId());
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($produto ? $produto->getNome() : 'Produto não encontrado', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo (int) $estoque->getQuantidade(); ?></td>
                        <td>R$ <?php echo number_format($estoque->getPreco(), 2, ',', '.'); ?></td>
                        <td>
                            <a href="/Pages/Stock/show.php?id=<?php echo (int) $estoque->getId(); ?>" class="btn btn-info btn-xs">Ver</a>
                            <a href="/Pages/Stock/edit.php?id=<?php echo (int) $estoque->getId(); ?>" class="btn btn-primary btn-xs">Editar</a>
                            <a href="/Service/Stock/delete_action.php?id=<?php echo (int) $estoque->getId(); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Tem certeza que deseja excluir este estoque?')">Excluir</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning">Nenhum estoque encontrado.</div>
    <?php } ?>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>