<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$page_title = 'Fornecedores';
$dao = $factory->getFornecedorDao();

$filtro = isset($_GET['filtro']) ? trim((string) $_GET['filtro']) : 'nome';
$busca = isset($_GET['busca']) ? trim((string) $_GET['busca']) : '';
$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

if ($q !== '' && $busca === '') {
    $busca = $q;
    $filtro = ctype_digit($q) ? 'codigo' : 'nome';
}

if ($busca !== '') {
    if ($filtro === 'codigo') {
        $fornecedores = $dao->buscaPorId((int) $busca);
        $fornecedores = $fornecedores ? [$fornecedores] : [];
    } else {
        $fornecedores = $dao->buscaPorNome($busca);
    }
} else {
    $fornecedores = $dao->buscaTodos();
}

include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <div class="clearfix" style="margin-bottom: 15px;">
        <a href="/Pages/Suppliers/create.php" class="btn btn-primary">Novo fornecedor</a>
    </div>

    <form method="get" class="form-inline" style="margin-bottom: 20px;">
        <div class="form-group">
            <select name="filtro" class="form-control">
                <option value="nome" <?php echo $filtro === 'codigo' ? '' : 'selected'; ?>>Nome</option>
                <option value="codigo" <?php echo $filtro === 'codigo' ? 'selected' : ''; ?>>Código</option>
            </select>
        </div>
        <div class="form-group">
            <input type="text" name="busca" value="<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Pesquisar fornecedor">
        </div>
        <button type="submit" class="btn btn-default">Buscar</button>
        <a href="/Pages/Suppliers/list.php" class="btn btn-link">Limpar</a>
    </form>

    <?php if ($fornecedores) { ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Endereço ID</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($fornecedores as $fornecedor) { ?>
                    <tr>
                        <td><?php echo $fornecedor->getId(); ?></td>
                        <td><?php echo htmlspecialchars($fornecedor->getNome(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($fornecedor->getTelefone(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($fornecedor->getEmail(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $fornecedor->getEnderecoId(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="/Pages/Suppliers/show.php?id=<?php echo $fornecedor->getId(); ?>" class="btn btn-info btn-xs">Ver</a>
                            <a href="/Pages/Suppliers/edit.php?id=<?php echo $fornecedor->getId(); ?>" class="btn btn-primary btn-xs">Editar</a>
                            <a href="/Service/Suppliers/delete_action.php?id=<?php echo $fornecedor->getId(); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Tem certeza que deseja excluir este fornecedor?')">Excluir</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning">Nenhum fornecedor encontrado.</div>
    <?php } ?>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
