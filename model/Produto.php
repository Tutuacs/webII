<?php

class Produto
{
    private $id;
    private $nome;
    private $descricao;
    private $foto;
    private $fornecedorId;
    private $estoqueId;

    public function __construct($id = null, $nome = null, $descricao = null, $foto = null, $fornecedorId = null, $estoqueId = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->foto = $foto;
        $this->fornecedorId = $fornecedorId;
        $this->estoqueId = $estoqueId;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }

    public function getDescricao() { return $this->descricao; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }

    public function getFoto() { return $this->foto; }
    public function setFoto($foto) { $this->foto = $foto; }

    public function getFornecedorId() { return $this->fornecedorId; }
    public function setFornecedorId($fornecedorId) { $this->fornecedorId = $fornecedorId; }

    public function getEstoqueId() { return $this->estoqueId; }
    public function setEstoqueId($estoqueId) { $this->estoqueId = $estoqueId; }
}
