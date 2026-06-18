<?php
interface IEstoqueDao extends DAO
{
    public function contaTodos();
    public function contaPorNome($nome);
    public function buscaTodosPaginado($limit, $offset);
    public function buscaPorNomePaginado($nome, $limit, $offset);
}

?>
