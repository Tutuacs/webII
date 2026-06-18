<?php
interface IUsuarioDao extends DAO
{
    public function buscaPorLogin($login);
    public function contaTodos();
    public function contaPorNome($nome);
    public function buscaTodosPaginado($limit, $offset);
    public function buscaPorNomePaginado($nome, $limit, $offset);
}

?>
