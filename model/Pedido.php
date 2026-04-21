<?php

class Pedido
{
    private $id;
    private $numero;
    private $dataPedido;
    private $dataEntrega;
    private $situacao;
    private $clienteId;

    public function __construct($id = null, $numero = null, $dataPedido = null, $dataEntrega = null, $situacao = null, $clienteId = null)
    {
        $this->id = $id;
        $this->numero = $numero;
        $this->dataPedido = $dataPedido;
        $this->dataEntrega = $dataEntrega;
        $this->situacao = $situacao;
        $this->clienteId = $clienteId;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNumero() { return $this->numero; }
    public function setNumero($numero) { $this->numero = $numero; }

    public function getDataPedido() { return $this->dataPedido; }
    public function setDataPedido($dataPedido) { $this->dataPedido = $dataPedido; }

    public function getDataEntrega() { return $this->dataEntrega; }
    public function setDataEntrega($dataEntrega) { $this->dataEntrega = $dataEntrega; }

    public function getSituacao() { return $this->situacao; }
    public function setSituacao($situacao) { $this->situacao = $situacao; }

    public function getClienteId() { return $this->clienteId; }
    public function setClienteId($clienteId) { $this->clienteId = $clienteId; }
}
