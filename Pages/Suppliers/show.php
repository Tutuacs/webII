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

$page_title = 'Detalhes do Fornecedor';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <h2><?php echo htmlspecialchars($fornecedor->getNome(), ENT_QUOTES, 'UTF-8'); ?></h2>
    <p><strong>Código:</strong> <?php echo $fornecedor->getId(); ?></p>
    <p><strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($fornecedor->getDescricao(), ENT_QUOTES, 'UTF-8')); ?></p>
    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($fornecedor->getTelefone(), ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($fornecedor->getEmail(), ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Endereço ID:</strong> <?php echo htmlspecialchars((string) $fornecedor->getEnderecoId(), ENT_QUOTES, 'UTF-8'); ?></p>
    <a href="/Pages/Suppliers/list.php" class="btn btn-default">Voltar</a>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
