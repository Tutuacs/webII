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
}
