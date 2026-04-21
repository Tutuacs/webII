<?php

abstract class ClasseDAO
{
    protected $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
}
