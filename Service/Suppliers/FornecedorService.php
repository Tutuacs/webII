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

    // ── Paginação ──────────────────────────────────────────────────────────────

    public function listarPaginado($page, $perPage)
    {
        $offset = ($page - 1) * $perPage;
        return $this->fornecedorDao->buscaTodosPaginado($perPage, $offset);
    }

    public function buscarPorNomePaginado($nome, $page, $perPage)
    {
        $offset = ($page - 1) * $perPage;
        return $this->fornecedorDao->buscaPorNomePaginado($nome, $perPage, $offset);
    }

    public function contarTodos()
    {
        return $this->fornecedorDao->contaTodos();
    }

    public function contarPorNome($nome)
    {
        return $this->fornecedorDao->contaPorNome($nome);
    }
}