<?php

class ProdutoService
{
    private $produtoDao;

    public function __construct(DaoFactory $factory)
    {
        $this->produtoDao = $factory->getProdutoDao();
    }

    public function listar()
    {
        return $this->produtoDao->buscaTodos();
    }

    public function buscarPorId($id)
    {
        return $this->produtoDao->buscaPorId($id);
    }

    public function buscarPorNome($nome)
    {
        return $this->produtoDao->buscaPorNome($nome);
    }

    public function salvar(Produto $produto)
    {
        return $this->produtoDao->insere($produto);
    }

    public function atualizar(Produto $produto)
    {
        return $this->produtoDao->altera($produto);
    }

    public function excluir($produto)
    {
        return $this->produtoDao->remove($produto);
    }
}
