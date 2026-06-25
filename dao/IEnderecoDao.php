<?php
interface IEnderecoDao extends DAO
{
    public function contaTodos();
    public function contaPorRua($rua);
    public function buscaTodosPaginado($limit, $offset);
    public function buscaPorRuaPaginado($rua, $limit, $offset);
    
    public function buscaPorRua($rua); 
}

?>
