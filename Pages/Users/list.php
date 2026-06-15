<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$page_title = 'Usuários';
$dao = $factory->getUsuarioDao();

$busca = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

if ($busca !== '') {
    $usuarios = [];

    if (ctype_digit($busca)) {
        $porId = $dao->buscaPorId((int) $busca);
        if ($porId) {
            $usuarios[] = $porId;
        }
    }

    $porNome = $dao->buscaPorNome($busca);
    foreach ($porNome as $u) {
        $jaExiste = false;
        foreach ($usuarios as $existente) {
            if ($existente->getId() === $u->getId()) {
                $jaExiste = true;
                break;
            }
        }
        if (!$jaExiste) {
            $usuarios[] = $u;
        }
    }
} else {
    $usuarios = $dao->buscaTodos();
}

include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <div class="clearfix" style="margin-bottom: 15px;">
        <a href="/Pages/Users/create.php" class="btn btn-primary">Novo usuário</a>
    </div>

    <form method="get" class="form-inline" style="margin-bottom: 20px;">
        <div class="form-group">
            <input type="text" name="q" value="<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Buscar por código ou nome..." style="min-width: 260px;">
        </div>
        <button type="submit" class="btn btn-default">Buscar</button>
        <?php if ($busca !== '') { ?>
            <a href="/Pages/Users/list.php" class="btn btn-link">Limpar</a>
        <?php } ?>
    </form>

    <?php if ($busca !== '') { ?>
        <p>Resultados para: <strong><?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?></strong></p>
    <?php } ?>

    <?php if ($usuarios) { ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Login</th>
                        <th>Nome</th>
                        <th>Perfil</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $usuario) { ?>
                    <tr>
                        <td><?php echo (int) $usuario->getId(); ?></td>
                        <td><?php echo htmlspecialchars($usuario->getLogin(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario->getNome(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario->getRole(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="/Pages/Users/show.php?id=<?php echo (int) $usuario->getId(); ?>" class="btn btn-info btn-xs">Ver</a>
                            <a href="/Pages/Users/edit.php?id=<?php echo (int) $usuario->getId(); ?>" class="btn btn-primary btn-xs">Editar</a>
                            <a href="/Service/Users/delete_action.php?id=<?php echo (int) $usuario->getId(); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning">Nenhum usuário encontrado.</div>
    <?php } ?>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>