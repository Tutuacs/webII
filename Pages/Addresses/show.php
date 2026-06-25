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
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><span class="glyphicon glyphicon-map-marker"></span> Endereço #<?php echo $endereco->getId(); ?></h3>
            </div>
            <div class="panel-body">
                <p><strong>Rua:</strong> <?php echo htmlspecialchars($endereco->getRua(), ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Número:</strong> <?php echo htmlspecialchars($endereco->getNumero(), ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Complemento:</strong> <?php echo htmlspecialchars($endereco->getComplemento(), ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Bairro:</strong> <?php echo htmlspecialchars($endereco->getBairro(), ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>CEP:</strong> <?php echo htmlspecialchars($endereco->getCep(), ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Cidade/UF:</strong> <?php echo htmlspecialchars($endereco->getCidade(), ENT_QUOTES, 'UTF-8'); ?> / <?php echo htmlspecialchars($endereco->getEstado(), ENT_QUOTES, 'UTF-8'); ?></p>
                
                <div style="margin-top: 20px;">
                    <a href="/Pages/Addresses/edit.php?id=<?php echo $endereco->getId(); ?>" class="btn btn-primary">Editar</a>
                    <a href="/Pages/Addresses/list.php" class="btn btn-default">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>