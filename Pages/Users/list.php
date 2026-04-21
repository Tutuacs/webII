<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$page_title = 'Usuários';
$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

if ($q !== '') {
    if (ctype_digit($q)) {
        $usuario = $factory->getUsuarioDao()->buscaPorId((int) $q);
        $usuarios = $usuario ? [$usuario] : [];
    } else {
        $usuarios = $factory->getUsuarioDao()->buscaPorNome($q);
    }
} else {
    $usuarios = $factory->getUsuarioDao()->buscaTodos();
}

include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <div class="clearfix" style="margin-bottom:15px;">
        <a href="/Pages/Users/create.php" class="btn btn-primary">Novo usuário</a>
    </div>

    <?php if ($q !== '') { ?>
        <p>Resultados para: <strong><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></strong></p>
    <?php } ?>

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
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
