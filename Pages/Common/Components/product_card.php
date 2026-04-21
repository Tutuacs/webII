<?php

function normalize_product_text(?string $text): string
{
    if ($text === null) {
        return '';
    }

    $normalized = $text;

    if (preg_match('/Ã.|Â./u', $normalized)) {
        $converted = @mb_convert_encoding($normalized, 'UTF-8', 'ISO-8859-1');
        if ($converted !== false && $converted !== '') {
            $normalized = $converted;
        }
    }

    return $normalized;
}

function get_product_image_src(Produto $produto): string
{
    $foto = $produto->getFoto();
    if ($foto !== null && $foto !== '') {
        return 'data:image/jpeg;base64,' . base64_encode($foto);
    }

    return 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="400" height="260"><rect width="100%" height="100%" fill="#f3f4f6"/><text x="50%" y="50%" text-anchor="middle" dominant-baseline="middle" font-size="22" fill="#6b7280">Sem imagem</text></svg>');
}

function render_product_card(Produto $produto, bool $isLogged, string $returnPath): void
{
    $nome = normalize_product_text($produto->getNome());
    $descricao = normalize_product_text((string) $produto->getDescricao());
    $addUrl = '/Service/Products/add_to_cart.php?produto_id=' . (int) $produto->getId() . '&return=' . urlencode($returnPath);
    $buttonLabel = $isLogged ? 'Adicionar ao carrinho' : 'Entrar para comprar';
    ?>
    <div class="panel panel-default product-card">
        <img class="product-image" src="<?php echo htmlspecialchars(get_product_image_src($produto), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="panel-body">
            <h4 class="product-title"><?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?></h4>
            <p class="text-muted product-description"><?php echo htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Produto #<?php echo (int) $produto->getId(); ?></strong></p>
            <a class="btn btn-primary btn-sm btn-block" href="<?php echo htmlspecialchars($addUrl, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $buttonLabel; ?></a>
        </div>
    </div>
    <?php
}
