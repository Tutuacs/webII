<?php

class EstoqueDAO extends ClasseDAO implements IEstoqueDao
{
    private $tableName = 'estoque';

    public function insere($estoque)
    {
        $stmt = $this->conn->prepare('INSERT INTO ' . $this->tableName . ' (quantidade, preco, produto_id) VALUES (:quantidade, :preco, :produto_id)');
        $stmt->bindValue(':quantidade', $estoque->getQuantidade());
        $stmt->bindValue(':preco', $estoque->getPreco());
        $stmt->bindValue(':produto_id', $estoque->getProdutoId());

        $executed = $stmt->execute();
        if ($executed) {
            $estoque->setId((int) $this->conn->lastInsertId());
        }

        return $executed;
    }

    public function altera(&$estoque)
    {
        $stmt = $this->conn->prepare('UPDATE ' . $this->tableName . ' SET quantidade = :quantidade, preco = :preco, produto_id = :produto_id WHERE id = :id');
        $stmt->bindValue(':quantidade', $estoque->getQuantidade());
        $stmt->bindValue(':preco', $estoque->getPreco());
        $stmt->bindValue(':produto_id', $estoque->getProdutoId());
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
        $stmt = $this->conn->prepare('SELECT id, quantidade, preco, produto_id FROM ' . $this->tableName . ' WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Estoque($row['id'], $row['quantidade'], $row['preco'], $row['produto_id']) : null;
    }

    public function buscaPorNome($nome)
    {
        $stmt = $this->conn->prepare('SELECT e.id, e.quantidade, e.preco, e.produto_id FROM ' . $this->tableName . ' e INNER JOIN produto p ON e.produto_id = p.id WHERE p.nome LIKE :nome ORDER BY p.nome, e.id');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();

        $estoques = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estoques[] = new Estoque($row['id'], $row['quantidade'], $row['preco'], $row['produto_id']);
        }

        return $estoques;
    }

    public function buscaPorProdutoId($produtoId)
    {
        $stmt = $this->conn->prepare('SELECT id, quantidade, preco, produto_id FROM ' . $this->tableName . ' WHERE produto_id = :produto_id ORDER BY id ASC');
        $stmt->bindValue(':produto_id', $produtoId);
        $stmt->execute();

        $estoques = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estoques[] = new Estoque($row['id'], $row['quantidade'], $row['preco'], $row['produto_id']);
        }

        return $estoques;
    }

    public function buscaTodos()
    {
        $stmt = $this->conn->prepare('SELECT id, quantidade, preco, produto_id FROM ' . $this->tableName . ' ORDER BY id ASC');
        $stmt->execute();

        $estoques = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estoques[] = new Estoque($row['id'], $row['quantidade'], $row['preco'], $row['produto_id']);
        }

        return $estoques;
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
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM ' . $this->tableName . ' e INNER JOIN produto p ON e.produto_id = p.id WHERE p.nome LIKE :nome');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function buscaTodosPaginado($limit, $offset)
    {
        $stmt = $this->conn->prepare('SELECT id, quantidade, preco, produto_id FROM ' . $this->tableName . ' ORDER BY id ASC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $estoques = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estoques[] = new Estoque($row['id'], $row['quantidade'], $row['preco'], $row['produto_id']);
        }

        return $estoques;
    }

    public function buscaPorNomePaginado($nome, $limit, $offset)
    {
        $stmt = $this->conn->prepare('SELECT e.id, e.quantidade, e.preco, e.produto_id FROM ' . $this->tableName . ' e INNER JOIN produto p ON e.produto_id = p.id WHERE p.nome LIKE :nome ORDER BY p.nome, e.id LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $estoques = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estoques[] = new Estoque($row['id'], $row['quantidade'], $row['preco'], $row['produto_id']);
        }

        return $estoques;
    }
}