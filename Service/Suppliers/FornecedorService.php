<?php

class FornecedorService
{
    private $fornecedorDao;

    public function __construct(DaoFactory $factory)
    {
        $this->fornecedorDao = $factory->getFornecedorDao();
    }

    public function listar()
    {
        return $this->fornecedorDao->buscaTodos();
    }

    public function buscarPorId($id)
    {
        return $this->fornecedorDao->buscaPorId($id);
    }

    public function buscarPorNome($nome)
    {
        return $this->fornecedorDao->buscaPorNome($nome);
    }

    public function salvar(Fornecedor $fornecedor)
    {
        return $this->fornecedorDao->insere($fornecedor);
    }

    public function atualizar(Fornecedor $fornecedor)
    {
        return $this->fornecedorDao->altera($fornecedor);
    }

    public function excluir($fornecedor)
    {
        return $this->fornecedorDao->remove($fornecedor);
    }
}
