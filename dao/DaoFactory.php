<?php

abstract class DaoFactory
{
    abstract protected function getConnection();

    abstract public function getUsuarioDao();

    abstract public function getClienteDao();

    abstract public function getEnderecoDao();

    abstract public function getFornecedorDao();

    abstract public function getProdutoDao();

    abstract public function getEstoqueDao();

    abstract public function getPedidoDao();

    abstract public function getItemPedidoDao();
}