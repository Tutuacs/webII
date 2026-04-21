<?php

class ItemPedido
{
    private $id;
    private $quantidade;
    private $preco;
    private $pedidoId;
    private $produtoId;

    public function __construct($id = null, $quantidade = null, $preco = null, $pedidoId = null, $produtoId = null)
    {
        $this->id = $id;
        $this->quantidade = $quantidade;
        $this->preco = $preco;
        $this->pedidoId = $pedidoId;
        $this->produtoId = $produtoId;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getQuantidade() { return $this->quantidade; }
    public function setQuantidade($quantidade) { $this->quantidade = $quantidade; }

    public function getPreco() { return $this->preco; }
    public function setPreco($preco) { $this->preco = $preco; }

    public function getPedidoId() { return $this->pedidoId; }
    public function setPedidoId($pedidoId) { $this->pedidoId = $pedidoId; }

    public function getProdutoId() { return $this->produtoId; }
    public function setProdutoId($produtoId) { $this->produtoId = $produtoId; }
}
