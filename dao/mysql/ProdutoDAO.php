<?php

class ProdutoDAO extends ClasseDAO implements DAO
{
    private $tableName = 'produto';

    public function insere($produto)
    {
        $stmt = $this->conn->prepare('INSERT INTO ' . $this->tableName . ' (nome, descricao, foto, fornecedor_id, estoque_id) VALUES (:nome, :descricao, :foto, :fornecedor_id, :estoque_id)');
        $stmt->bindValue(':nome', $produto->getNome());
        $stmt->bindValue(':descricao', $produto->getDescricao());
        $stmt->bindValue(':foto', $produto->getFoto(), PDO::PARAM_LOB);
        $stmt->bindValue(':fornecedor_id', $produto->getFornecedorId());
        $stmt->bindValue(':estoque_id', $produto->getEstoqueId());

        return $stmt->execute();
    }

    public function altera(&$produto)
    {
        $stmt = $this->conn->prepare('UPDATE ' . $this->tableName . ' SET nome = :nome, descricao = :descricao, foto = :foto, fornecedor_id = :fornecedor_id, estoque_id = :estoque_id WHERE id = :id');
        $stmt->bindValue(':nome', $produto->getNome());
        $stmt->bindValue(':descricao', $produto->getDescricao());
        $stmt->bindValue(':foto', $produto->getFoto(), PDO::PARAM_LOB);
        $stmt->bindValue(':fornecedor_id', $produto->getFornecedorId());
        $stmt->bindValue(':estoque_id', $produto->getEstoqueId());
        $stmt->bindValue(':id', $produto->getId());

        return $stmt->execute();
    }

    public function remove($produto)
    {
        return $this->removePorId($produto->getId());
    }

    public function removePorId($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM ' . $this->tableName . ' WHERE id = :id');
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function buscaPorId($id)
    {
        $stmt = $this->conn->prepare('SELECT id, nome, descricao, foto, fornecedor_id, estoque_id FROM ' . $this->tableName . ' WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Produto($row['id'], $row['nome'], $row['descricao'], $row['foto'], $row['fornecedor_id'], $row['estoque_id']) : null;
    }

    public function buscaPorNome($nome)
    {
        $stmt = $this->conn->prepare('SELECT id, nome, descricao, foto, fornecedor_id, estoque_id FROM ' . $this->tableName . ' WHERE nome LIKE :nome ORDER BY nome');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();

        $produtos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $produtos[] = new Produto($row['id'], $row['nome'], $row['descricao'], $row['foto'], $row['fornecedor_id'], $row['estoque_id']);
        }

        return $produtos;
    }

    public function buscaTodos()
    {
        $stmt = $this->conn->prepare('SELECT id, nome, descricao, foto, fornecedor_id, estoque_id FROM ' . $this->tableName . ' ORDER BY id ASC');
        $stmt->execute();

        $produtos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $produtos[] = new Produto($row['id'], $row['nome'], $row['descricao'], $row['foto'], $row['fornecedor_id'], $row['estoque_id']);
        }

        return $produtos;
    }
}
