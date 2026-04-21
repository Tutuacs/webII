<?php

interface DAO
{
    public function insere($entidade);

    public function altera(&$entidade);

    public function remove($entidade);

    public function removePorId($id);

    public function buscaPorId($id);

    public function buscaPorNome($nome);

    public function buscaTodos();
}
