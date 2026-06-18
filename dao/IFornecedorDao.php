<?php
interface IFornecedorDao extends DAO
{
    public function contaTodos();
    public function contaPorNome($nome);
    public function buscaTodosPaginado($limit, $offset);
    public function buscaPorNomePaginado($nome, $limit, $offset);
}

?>
