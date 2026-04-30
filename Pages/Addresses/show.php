<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$endereco = $factory->getEnderecoDao()->buscaPorId($id);

if (!$endereco) {
    header('Location: /Pages/Addresses/list.php');
    exit;
}

$page_title = 'Detalhes do Endereço';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section class="row">
    <div class="col-md-8">
        <div class="panel panel-default" style="padding:20px;">
            <h3><?php echo htmlspecialchars($endereco->getNome(), ENT_QUOTES, 'UTF-8'); ?></h3>
            <p><strong>Código:</strong> <?php echo $endereco->getId(); ?></p>
            <p><strong>Descrição:</strong> <?php echo htmlspecialchars($endereco->getDescricao(), ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($endereco->getTelefone(), ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($endereco->getEmail(), ENT_QUOTES, 'UTF-8'); ?></p>
            <a href="/Pages/Addresses/edit.php?id=<?php echo $endereco->getId(); ?>" class="btn btn-primary">Editar</a>
            <a href="/Pages/Addresses/list.php" class="btn btn-default">Voltar</a>
        </div>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php';
