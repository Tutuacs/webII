<?php

class EstoqueService
{
    private $estoqueDao;

    public function __construct(DaoFactory $factory)
    {
        $this->estoqueDao = $factory->getEstoqueDao();
    }

    public function listar()
    {
        return $this->estoqueDao->buscaTodos();
    }

    public function buscarPorId($id)
    {
        return $this->estoqueDao->buscaPorId($id);
    }

    public function buscarPorNome($nome)
    {
        return $this->estoqueDao->buscaPorNome($nome);
    }

    public function salvar(Estoque $estoque)
    {
        return $this->estoqueDao->insere($estoque);
    }

    public function atualizar(Estoque $estoque)
    {
        return $this->estoqueDao->altera($estoque);
    }

    public function excluir($estoque)
    {
        return $this->estoqueDao->remove($estoque);
    }
}
