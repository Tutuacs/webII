<?php

class Cliente
{
    private $id;
    private $nome;
    private $telefone;
    private $email;
    private $cartaoCredito;
    private $enderecoId;

    public function __construct($id = null, $nome = null, $telefone = null, $email = null, $cartaoCredito = null, $enderecoId = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->telefone = $telefone;
        $this->email = $email;
        $this->cartaoCredito = $cartaoCredito;
        $this->enderecoId = $enderecoId;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }

    public function getTelefone() { return $this->telefone; }
    public function setTelefone($telefone) { $this->telefone = $telefone; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getCartaoCredito() { return $this->cartaoCredito; }
    public function setCartaoCredito($cartaoCredito) { $this->cartaoCredito = $cartaoCredito; }

    public function getEnderecoId() { return $this->enderecoId; }
    public function setEnderecoId($enderecoId) { $this->enderecoId = $enderecoId; }
}
