<?php
interface UsuarioDaoInterface extends DAO 
{
    // Aqui você só coloca o que for EXCLUSIVO do usuário
    public function buscaPorLogin($login);
}
?>