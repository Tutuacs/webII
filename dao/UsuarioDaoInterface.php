<?php
interface UsuarioDaoInterface extends DAO 
{
    public function buscaPorLogin($login);
}
?>