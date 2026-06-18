<?php

class ProdutoDAO extends ClasseDAO implements IProdutoDao
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

        $executed = $stmt->execute();
        if ($executed) {
            $produto->setId((int) $this->conn->lastInsertId());
        }

        return $executed;
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

    // ── Paginação ──────────────────────────────────────────────────────────────

    public function contaTodos()
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM ' . $this->tableName);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function contaPorNome($nome)
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE nome LIKE :nome');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function buscaTodosPaginado($limit, $offset)
    {
        $stmt = $this->conn->prepare('SELECT id, nome, descricao, foto, fornecedor_id, estoque_id FROM ' . $this->tableName . ' ORDER BY id ASC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $produtos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $produtos[] = new Produto($row['id'], $row['nome'], $row['descricao'], $row['foto'], $row['fornecedor_id'], $row['estoque_id']);
        }

        return $produtos;
    }

    public function buscaPorNomePaginado($nome, $limit, $offset)
    {
        $stmt = $this->conn->prepare('SELECT id, nome, descricao, foto, fornecedor_id, estoque_id FROM ' . $this->tableName . ' WHERE nome LIKE :nome ORDER BY nome LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $produtos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $produtos[] = new Produto($row['id'], $row['nome'], $row['descricao'], $row['foto'], $row['fornecedor_id'], $row['estoque_id']);
        }

        return $produtos;
    }
}