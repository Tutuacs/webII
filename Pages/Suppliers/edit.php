<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$fornecedor = $factory->getFornecedorDao()->buscaPorId($id);

if (!$fornecedor) {
    header('Location: /Pages/Suppliers/list.php');
    exit;
}

$page_title = 'Editar Fornecedor';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section class="row">
    <div class="col-md-8">
        <form action="/Service/Suppliers/update_action.php" method="post" class="panel panel-default" style="padding:20px;">
            <input type="hidden" name="id" value="<?php echo $fornecedor->getId(); ?>">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" class="form-control" value="<?php echo htmlspecialchars($fornecedor->getNome(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="4"><?php echo htmlspecialchars($fornecedor->getDescricao(), ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" class="form-control" value="<?php echo htmlspecialchars($fornecedor->getTelefone(), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($fornecedor->getEmail(), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="endereco_id">Endereço ID</label>
                <input type="number" id="endereco_id" name="endereco_id" class="form-control" min="1" value="<?php echo htmlspecialchars((string) $fornecedor->getEnderecoId(), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="/Pages/Suppliers/list.php" class="btn btn-default">Cancelar</a>
        </form>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
