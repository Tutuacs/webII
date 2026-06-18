<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$page_title = 'Estoque';
$service    = new EstoqueService($factory);
$produtoDao = $factory->getProdutoDao();

const PER_PAGE = 10;

$busca = isset($_GET['q'])    ? trim((string) $_GET['q'])   : '';
$page  = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

// ── Busca com paginação ────────────────────────────────────────────────────────
$buscaPorIdExato = false;

if ($busca !== '' && ctype_digit($busca)) {
    $porId = $service->buscarPorId((int) $busca);
    if ($porId) {
        $buscaPorIdExato = true;
        $estoques = [$porId];

        $porNome = $service->buscarPorNomePaginado($busca, $page, PER_PAGE);
        foreach ($porNome as $e) {
            if ($e->getId() !== $porId->getId()) {
                $estoques[] = $e;
            }
        }
        $total      = count($estoques);
        $totalPages = 1;
    }
}

if (!$buscaPorIdExato) {
    if ($busca !== '') {
        $total    = $service->contarPorNome($busca);
        $estoques = $service->buscarPorNomePaginado($busca, $page, PER_PAGE);
    } else {
        $total    = $service->contarTodos();
        $estoques = $service->listarPaginado($page, PER_PAGE);
    }
    $totalPages = (int) ceil($total / PER_PAGE);
    $page       = min($page, max(1, $totalPages));
}

function paginaUrl($p, $q) {
    $params = ['page' => $p];
    if ($q !== '') {
        $params['q'] = $q;
    }
    return '/Pages/Stock/list.php?' . http_build_query($params);
}

include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <div class="clearfix" style="margin-bottom: 15px;">
        <a href="/Pages/Stock/create.php" class="btn btn-primary">Novo estoque</a>
    </div>

    <form method="get" class="form-inline" style="margin-bottom: 20px;">
        <div class="form-group">
            <input type="text" name="q" value="<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-control" placeholder="Buscar por ID ou nome do produto..." style="min-width: 260px;">
        </div>
        <button type="submit" class="btn btn-default">Buscar</button>
        <?php if ($busca !== '') { ?>
            <a href="/Pages/Stock/list.php" class="btn btn-link">Limpar</a>
        <?php } ?>
    </form>

    <?php if ($busca !== '') { ?>
        <p>Resultados para: <strong><?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?></strong>
           — <?php echo $total; ?> registro(s) encontrado(s)</p>
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
                            <a href="/Service/Stock/delete_action.php?id=<?php echo (int) $estoque->getId(); ?>" class="btn btn-danger btn-xs"
                               onclick="return confirm('Tem certeza que deseja excluir este estoque?')">Excluir</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1) { ?>
        <nav>
            <ul class="pagination">

                <li class="<?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a href="<?php echo $page > 1 ? htmlspecialchars(paginaUrl($page - 1, $busca)) : '#'; ?>">
                        &laquo;
                    </a>
                </li>

                <?php
                $start = max(1, $page - 2);
                $end   = min($totalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++) { ?>
                    <li class="<?php echo $i === $page ? 'active' : ''; ?>">
                        <a href="<?php echo htmlspecialchars(paginaUrl($i, $busca)); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php } ?>

                <li class="<?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                    <a href="<?php echo $page < $totalPages ? htmlspecialchars(paginaUrl($page + 1, $busca)) : '#'; ?>">
                        &raquo;
                    </a>
                </li>

            </ul>
        </nav>
        <p class="text-muted small">
            Página <?php echo $page; ?> de <?php echo $totalPages; ?>
            — <?php echo $total; ?> registro(s) no total
        </p>
        <?php } ?>

    <?php } else { ?>
        <div class="alert alert-warning">Nenhum estoque encontrado.</div>
    <?php } ?>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>