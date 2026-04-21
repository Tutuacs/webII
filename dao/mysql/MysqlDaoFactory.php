<?php

class MysqlDaoFactory extends DaoFactory
{
    private $host;
    private $dbName;
    private $port;
    private $username;
    private $password;
    private $connection = null;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: 'mysql';
        $this->dbName = getenv('DB_NAME') ?: 'dao3';
        $this->port = getenv('DB_PORT') ?: '3306';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: 'root';
    }

    protected function getConnection()
    {
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $this->host,
            $this->port,
            $this->dbName
        );

        $this->connection = new PDO(
            $dsn,
            $this->username,
            $this->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
            ]
        );

        return $this->connection;
    }

    public function getUsuarioDao()
    {
        return new UsuarioDAO($this->getConnection());
    }

    public function getClienteDao()
    {
        return new ClienteDAO($this->getConnection());
    }

    public function getEnderecoDao()
    {
        return new EnderecoDAO($this->getConnection());
    }

    public function getFornecedorDao()
    {
        return new FornecedorDAO($this->getConnection());
    }

    public function getProdutoDao()
    {
        return new ProdutoDAO($this->getConnection());
    }

    public function getEstoqueDao()
    {
        return new EstoqueDAO($this->getConnection());
    }

    public function getPedidoDao()
    {
        return new PedidoDAO($this->getConnection());
    }

    public function getItemPedidoDao()
    {
        return new ItemPedidoDAO($this->getConnection());
    }
}
