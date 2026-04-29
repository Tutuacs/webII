<?php

class EstoqueDAO extends ClasseDAO implements IEstoqueDao
{
    private $tableName = 'estoque';

    public function insere($estoque)
    {
        $stmt = $this->conn->prepare('INSERT INTO ' . $this->tableName . ' (quantidade, preco) VALUES (:quantidade, :preco)');
        $stmt->bindValue(':quantidade', $estoque->getQuantidade());
        $stmt->bindValue(':preco', $estoque->getPreco());

        return $stmt->execute();
    }

    public function altera(&$estoque)
    {
        $stmt = $this->conn->prepare('UPDATE ' . $this->tableName . ' SET quantidade = :quantidade, preco = :preco WHERE id = :id');
        $stmt->bindValue(':quantidade', $estoque->getQuantidade());
        $stmt->bindValue(':preco', $estoque->getPreco());
        $stmt->bindValue(':id', $estoque->getId());

        return $stmt->execute();
    }

    public function remove($estoque)
    {
        return $this->removePorId($estoque->getId());
    }

    public function removePorId($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM ' . $this->tableName . ' WHERE id = :id');
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function buscaPorId($id)
    {
        $stmt = $this->conn->prepare('SELECT id, quantidade, preco FROM ' . $this->tableName . ' WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Estoque($row['id'], $row['quantidade'], $row['preco']) : null;
    }

    public function buscaPorNome($nome)
    {
        $stmt = $this->conn->prepare('SELECT id, quantidade, preco FROM ' . $this->tableName . ' WHERE CAST(id AS CHAR) LIKE :nome ORDER BY id');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();

        $estoques = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estoques[] = new Estoque($row['id'], $row['quantidade'], $row['preco']);
        }

        return $estoques;
    }

    public function buscaTodos()
    {
        $stmt = $this->conn->prepare('SELECT id, quantidade, preco FROM ' . $this->tableName . ' ORDER BY id ASC');
        $stmt->execute();

        $estoques = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estoques[] = new Estoque($row['id'], $row['quantidade'], $row['preco']);
        }

        return $estoques;
    }
}
