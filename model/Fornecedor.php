<?php

class Fornecedor
{
    private $id;
    private $nome;
    private $descricao;
    private $telefone;
    private $email;
    private $enderecoId;

    public function __construct($id = null, $nome = null, $descricao = null, $telefone = null, $email = null, $enderecoId = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->telefone = $telefone;
        $this->email = $email;
        $this->enderecoId = $enderecoId;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }

    public function getDescricao() { return $this->descricao; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }

    public function getTelefone() { return $this->telefone; }
    public function setTelefone($telefone) { $this->telefone = $telefone; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getEnderecoId() { return $this->enderecoId; }
    public function setEnderecoId($enderecoId) { $this->enderecoId = $enderecoId; }
}
