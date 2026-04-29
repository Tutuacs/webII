<?php
interface IUsuarioDao extends DAO
{
    public function buscaPorLogin($login);
}

?>
