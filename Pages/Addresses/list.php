<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$page_title = 'Endereços';
$service    = new EnderecoService($factory);

const PER_PAGE = 10;

$busca = isset($_GET['q'])    ? trim((string) $_GET['q'])   : '';
$page  = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

// ── Busca com paginação ────────────────────────────────────────────────────────
$buscaPorIdExato = false;

if ($busca !== '' && ctype_digit($busca)) {
    $porId = $service->buscarPorId((int) $busca);
    if ($porId) {
        $buscaPorIdExato = true;
        $enderecos = [$porId];

        $porNome = $service->buscarPorNomePaginado($busca, $page, PER_PAGE);
        foreach ($porNome as $e) {
            if ($e->getId() !== $porId->getId()) {
                $enderecos[] = $e;
            }
        }
        $total      = count($enderecos);
        $totalPages = 1;
    }
}

if (!$buscaPorIdExato) {
    if ($busca !== '') {
        $total     = $service->contarPorNome($busca);
        $enderecos = $service->buscarPorNomePaginado($busca, $page, PER_PAGE);
    } else {
        $total     = $service->contarTodos();
        $enderecos = $service->listarPaginado($page, PER_PAGE);
    }
    $totalPages = (int) ceil($total / PER_PAGE);
    $page       = min($page, max(1, $totalPages));
}

function paginaUrl($p, $q) {
    $params = ['page' => $p];
    if ($q !== '') {
        $params['q'] = $q;
    }
    return '/Pages/Addresses/list.php?' . http_build_query($params);
}

include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <div class="clearfix" style="margin-bottom: 15px;">
        <a href="/Pages/Addresses/create.php" class="btn btn-primary">Novo endereço</a>
    </div>

    <form method="get" class="form-inline" style="margin-bottom: 20px;">
        <div class="form-group">
            <input type="text" name="q" value="<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-control" placeholder="Buscar por código ou nome..." style="min-width: 260px;">
        </div>
        <button type="submit" class="btn btn-default">Buscar</button>
        <?php if ($busca !== '') { ?>
            <a href="/Pages/Addresses/list.php" class="btn btn-link">Limpar</a>
        <?php } ?>
    </form>

    <?php if ($busca !== '') { ?>
        <p>Resultados para: <strong><?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?></strong>
           — <?php echo $total; ?> registro(s) encontrado(s)</p>
    <?php } ?>

    <?php if ($enderecos) { ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($enderecos as $endereco) { ?>
                    <tr>
                        <td><?php echo (int) $endereco->getId(); ?></td>
                        <td><?php echo htmlspecialchars($endereco->getNome(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($endereco->getDescricao(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($endereco->getTelefone(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($endereco->getEmail(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="/Pages/Addresses/show.php?id=<?php echo (int) $endereco->getId(); ?>" class="btn btn-info btn-xs">Ver</a>
                            <a href="/Pages/Addresses/edit.php?id=<?php echo (int) $endereco->getId(); ?>" class="btn btn-primary btn-xs">Editar</a>
                            <a href="/Service/Addresses/delete_action.php?id=<?php echo (int) $endereco->getId(); ?>" class="btn btn-danger btn-xs"
                               onclick="return confirm('Tem certeza que deseja excluir este endereço?')">Excluir</a>
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
        <div class="alert alert-warning">Nenhum endereço encontrado.</div>
    <?php } ?>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>