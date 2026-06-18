<?php

class ClienteDAO extends ClasseDAO implements IClienteDao
{
    private $tableName = 'cliente';

    public function insere($cliente)
    {
        $stmt = $this->conn->prepare('INSERT INTO ' . $this->tableName . ' (nome, telefone, email, cartao_credito, endereco_id) VALUES (:nome, :telefone, :email, :cartao_credito, :endereco_id)');
        $stmt->bindValue(':nome', $cliente->getNome());
        $stmt->bindValue(':telefone', $cliente->getTelefone());
        $stmt->bindValue(':email', $cliente->getEmail());
        $stmt->bindValue(':cartao_credito', $cliente->getCartaoCredito());
        $stmt->bindValue(':endereco_id', $cliente->getEnderecoId());

        return $stmt->execute();
    }

    public function altera(&$cliente)
    {
        $stmt = $this->conn->prepare('UPDATE ' . $this->tableName . ' SET nome = :nome, telefone = :telefone, email = :email, cartao_credito = :cartao_credito, endereco_id = :endereco_id WHERE id = :id');
        $stmt->bindValue(':nome', $cliente->getNome());
        $stmt->bindValue(':telefone', $cliente->getTelefone());
        $stmt->bindValue(':email', $cliente->getEmail());
        $stmt->bindValue(':cartao_credito', $cliente->getCartaoCredito());
        $stmt->bindValue(':endereco_id', $cliente->getEnderecoId());
        $stmt->bindValue(':id', $cliente->getId());

        return $stmt->execute();
    }

    public function remove($cliente)
    {
        return $this->removePorId($cliente->getId());
    }

    public function removePorId($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM ' . $this->tableName . ' WHERE id = :id');
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function buscaPorId($id)
    {
        $stmt = $this->conn->prepare('SELECT id, nome, telefone, email, cartao_credito, endereco_id FROM ' . $this->tableName . ' WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Cliente($row['id'], $row['nome'], $row['telefone'], $row['email'], $row['cartao_credito'], $row['endereco_id']) : null;
    }

    public function buscaPorNome($nome)
    {
        $stmt = $this->conn->prepare('SELECT id, nome, telefone, email, cartao_credito, endereco_id FROM ' . $this->tableName . ' WHERE nome LIKE :nome ORDER BY nome');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();

        $clientes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $clientes[] = new Cliente($row['id'], $row['nome'], $row['telefone'], $row['email'], $row['cartao_credito'], $row['endereco_id']);
        }

        return $clientes;
    }

    public function buscaTodos()
    {
        $stmt = $this->conn->prepare('SELECT id, nome, telefone, email, cartao_credito, endereco_id FROM ' . $this->tableName . ' ORDER BY id ASC');
        $stmt->execute();

        $clientes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $clientes[] = new Cliente($row['id'], $row['nome'], $row['telefone'], $row['email'], $row['cartao_credito'], $row['endereco_id']);
        }

        return $clientes;
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
        $stmt = $this->conn->prepare('SELECT id, nome, telefone, email, cartao_credito, endereco_id FROM ' . $this->tableName . ' ORDER BY id ASC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $clientes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $clientes[] = new Cliente($row['id'], $row['nome'], $row['telefone'], $row['email'], $row['cartao_credito'], $row['endereco_id']);
        }

        return $clientes;
    }

    public function buscaPorNomePaginado($nome, $limit, $offset)
    {
        $stmt = $this->conn->prepare('SELECT id, nome, telefone, email, cartao_credito, endereco_id FROM ' . $this->tableName . ' WHERE nome LIKE :nome ORDER BY nome LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $clientes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $clientes[] = new Cliente($row['id'], $row['nome'], $row['telefone'], $row['email'], $row['cartao_credito'], $row['endereco_id']);
        }

        return $clientes;
    }
}