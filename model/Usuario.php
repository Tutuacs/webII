<?php

class Usuario
{
    private $id;
    private $login;
    private $senha;
    private $nome;
    private $role;

    public function __construct($id = null, $login = null, $senha = null, $nome = null, $role = 'INTERNO')
    {
        $this->id = $id;
        $this->login = $login;
        $this->senha = $senha;
        $this->nome = $nome;
        $this->role = $role;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }
}