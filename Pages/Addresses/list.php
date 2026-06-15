<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$page_title = 'Endereços';
$dao = $factory->getEnderecoDao();

$busca = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

if ($busca !== '') {
    $enderecos = [];

    if (ctype_digit($busca)) {
        $porId = $dao->buscaPorId((int) $busca);
        if ($porId) {
            $enderecos[] = $porId;
        }
    }

    $porNome = $dao->buscaPorNome($busca);
    foreach ($porNome as $e) {
        $jaExiste = false;
        foreach ($enderecos as $existente) {
            if ($existente->getId() === $e->getId()) {
                $jaExiste = true;
                break;
            }
        }
        if (!$jaExiste) {
            $enderecos[] = $e;
        }
    }
} else {
    $enderecos = $dao->buscaTodos();
}

include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <div class="clearfix" style="margin-bottom: 15px;">
        <a href="/Pages/Addresses/create.php" class="btn btn-primary">Novo endereço</a>
    </div>

    <form method="get" class="form-inline" style="margin-bottom: 20px;">
        <div class="form-group">
            <input type="text" name="q" value="<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Buscar por código ou nome..." style="min-width: 260px;">
        </div>
        <button type="submit" class="btn btn-default">Buscar</button>
        <?php if ($busca !== '') { ?>
            <a href="/Pages/Addresses/list.php" class="btn btn-link">Limpar</a>
        <?php } ?>
    </form>

    <?php if ($busca !== '') { ?>
        <p>Resultados para: <strong><?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?></strong></p>
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
                            <a href="/Service/Addresses/delete_action.php?id=<?php echo (int) $endereco->getId(); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Tem certeza que deseja excluir este endereço?')">Excluir</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning">Nenhum endereço encontrado.</div>
    <?php } ?>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>