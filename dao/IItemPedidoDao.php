<?php
interface IItemPedidoDao extends DAO
{
    public function contaTodos();
    public function contaPorPedidoId($pedidoId);
    public function buscaTodosPaginado($limit, $offset);
    public function buscaPorPedidoIdPaginado($pedidoId, $limit, $offset);
}

?>
