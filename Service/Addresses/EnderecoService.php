<?php

class EnderecoService
{
    private $enderecoDao;

    public function __construct(DaoFactory $factory)
    {
        $this->enderecoDao = $factory->getEnderecoDao();
    }

    public function listar()
    {
        return $this->enderecoDao->buscaTodos();
    }

    public function buscarPorId($id)
    {
        return $this->enderecoDao->buscaPorId($id);
    }

    public function buscarPorRua($rua)
    {
        return $this->enderecoDao->buscaPorRua($rua);
    }

    public function buscarPorNome($nome)
    {
        return $this->enderecoDao->buscaPorNome($nome);
    }

    public function salvar(Endereco $endereco)
    {
        return $this->enderecoDao->insere($endereco);
    }

    public function atualizar(Endereco $endereco)
    {
        return $this->enderecoDao->altera($endereco);
    }

    public function excluir($endereco)
    {
        return $this->enderecoDao->remove($endereco);
    }

    public function excluirPorId($id)
    {
        return $this->enderecoDao->removePorId($id);
    }

    // ── Paginação ──────────────────────────────────────────────────────────────

    public function listarPaginado($page, $perPage)
    {
        $offset = ($page - 1) * $perPage;
        return $this->enderecoDao->buscaTodosPaginado($perPage, $offset);
    }

    public function buscarPorRuaPaginado($rua, $page, $perPage)
    {
        $offset = ($page - 1) * $perPage;
        return $this->enderecoDao->buscaPorRuaPaginado($rua, $perPage, $offset);
    }

    public function buscarPorNomePaginado($nome, $page, $perPage)
    {
        $offset = ($page - 1) * $perPage;
        return $this->enderecoDao->buscaPorRuaPaginado($nome, $perPage, $offset);
    }

    public function contarTodos()
    {
        return $this->enderecoDao->contaTodos();
    }

    public function contarPorRua($rua)
    {
        return $this->enderecoDao->contaPorRua($rua);
    }

    public function contarPorNome($nome)
    {
        return $this->enderecoDao->contaPorRua($nome);
    }
}