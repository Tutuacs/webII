<?php

class UsuarioService
{
    private $usuarioDao;

    public function __construct(DaoFactory $factory)
    {
        $this->usuarioDao = $factory->getUsuarioDao();
    }

    public function listar()
    {
        return $this->usuarioDao->buscaTodos();
    }

    public function buscarPorId($id)
    {
        return $this->usuarioDao->buscaPorId($id);
    }

    public function buscarPorNome($nome)
    {
        return $this->usuarioDao->buscaPorNome($nome);
    }

    public function salvar(Usuario $usuario)
    {
        return $this->usuarioDao->insere($usuario);
    }

    public function atualizar(Usuario $usuario)
    {
        return $this->usuarioDao->altera($usuario);
    }

    public function excluir($usuario)
    {
        return $this->usuarioDao->remove($usuario);
    }

    // ── Paginação ──────────────────────────────────────────────────────────────

    public function listarPaginado($page, $perPage)
    {
        $offset = ($page - 1) * $perPage;
        return $this->usuarioDao->buscaTodosPaginado($perPage, $offset);
    }

    public function buscarPorNomePaginado($nome, $page, $perPage)
    {
        $offset = ($page - 1) * $perPage;
        return $this->usuarioDao->buscaPorNomePaginado($nome, $perPage, $offset);
    }

    public function contarTodos()
    {
        return $this->usuarioDao->contaTodos();
    }

    public function contarPorNome($nome)
    {
        return $this->usuarioDao->contaPorNome($nome);
    }
}