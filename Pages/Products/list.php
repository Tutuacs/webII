<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$page_title = 'Produtos';
$service = new ProdutoService($factory);

const PER_PAGE = 10;

$busca   = isset($_GET['q'])    ? trim((string) $_GET['q'])   : '';
$page    = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

// ── Busca com paginação ────────────────────────────────────────────────────────
// Quando a busca for por ID exato (número), mostra só aquele registro sem paginar
$buscaPorIdExato = false;

if ($busca !== '' && ctype_digit($busca)) {
    $porId = $service->buscarPorId((int) $busca);
    if ($porId) {
        // Verifica se o nome também bate — evita retornar só pelo ID quando o
        // usuário quis buscar por nome numérico
        $buscaPorIdExato = true;
        $produtos   = [$porId];
        $total      = 1;
        $totalPages = 1;

        // Ainda mescla resultados por nome caso existam
        $porNome = $service->buscarPorNomePaginado($busca, $page, PER_PAGE);
        foreach ($porNome as $p) {
            if ($p->getId() !== $porId->getId()) {
                $produtos[] = $p;
            }
        }
        $total      = count($produtos);
        $totalPages = 1;
    }
}

if (!$buscaPorIdExato) {
    if ($busca !== '') {
        $total   = $service->contarPorNome($busca);
        $produtos = $service->buscarPorNomePaginado($busca, $page, PER_PAGE);
    } else {
        $total   = $service->contarTodos();
        $produtos = $service->listarPaginado($page, PER_PAGE);
    }
    $totalPages = (int) ceil($total / PER_PAGE);
    $page       = min($page, max(1, $totalPages));
}

// ── Helper: monta URL preservando ?q= e trocando ?page= ───────────────────────
function paginaUrl($p, $q) {
    $params = ['page' => $p];
    if ($q !== '') {
        $params['q'] = $q;
    }
    return '/Pages/Products/list.php?' . http_build_query($params);
}

include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <div class="clearfix" style="margin-bottom: 15px;">
        <a href="/Pages/Products/create.php" class="btn btn-primary">Novo produto</a>
    </div>

    <form method="get" class="form-inline" style="margin-bottom: 20px;">
        <div class="form-group">
            <input type="text" name="q" value="<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-control" placeholder="Buscar por código ou nome..." style="min-width: 260px;">
        </div>
        <button type="submit" class="btn btn-default">Buscar</button>
        <?php if ($busca !== '') { ?>
            <a href="/Pages/Products/list.php" class="btn btn-link">Limpar</a>
        <?php } ?>
    </form>

    <?php if ($busca !== '') { ?>
        <p>Resultados para: <strong><?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?></strong>
           — <?php echo $total; ?> registro(s) encontrado(s)</p>
    <?php } ?>

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
                            <a href="/Service/Products/delete_action.php?id=<?php echo (int) $produto->getId(); ?>" class="btn btn-danger btn-xs"
                               onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
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
                // Janela de no máximo 5 páginas ao redor da atual
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
        <div class="alert alert-warning">Nenhum produto encontrado.</div>
    <?php } ?>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>