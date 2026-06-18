<?php

class EnderecoDAO extends ClasseDAO implements IEnderecoDao
{
    private $tableName = 'endereco';

    public function insere($endereco)
    {
        $stmt = $this->conn->prepare('INSERT INTO ' . $this->tableName . ' (nome, descricao, telefone, email) VALUES (:nome, :descricao, :telefone, :email)');
        $stmt->bindValue(':nome', $endereco->getNome());
        $stmt->bindValue(':descricao', $endereco->getDescricao());
        $stmt->bindValue(':telefone', $endereco->getTelefone());
        $stmt->bindValue(':email', $endereco->getEmail());

        return $stmt->execute();
    }

    public function altera(&$endereco)
    {
        $stmt = $this->conn->prepare('UPDATE ' . $this->tableName . ' SET nome = :nome, descricao = :descricao, telefone = :telefone, email = :email WHERE id = :id');
        $stmt->bindValue(':nome', $endereco->getNome());
        $stmt->bindValue(':descricao', $endereco->getDescricao());
        $stmt->bindValue(':telefone', $endereco->getTelefone());
        $stmt->bindValue(':email', $endereco->getEmail());
        $stmt->bindValue(':id', $endereco->getId());

        return $stmt->execute();
    }

    public function remove($endereco)
    {
        return $this->removePorId($endereco->getId());
    }

    public function removePorId($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM ' . $this->tableName . ' WHERE id = :id');
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function buscaPorId($id)
    {
        $stmt = $this->conn->prepare('SELECT id, nome, descricao, telefone, email FROM ' . $this->tableName . ' WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Endereco($row['id'], $row['nome'], $row['descricao'], $row['telefone'], $row['email']) : null;
    }

    public function buscaPorNome($nome)
    {
        $stmt = $this->conn->prepare('SELECT id, nome, descricao, telefone, email FROM ' . $this->tableName . ' WHERE nome LIKE :nome ORDER BY nome');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();

        $enderecos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $enderecos[] = new Endereco($row['id'], $row['nome'], $row['descricao'], $row['telefone'], $row['email']);
        }

        return $enderecos;
    }

    public function buscaTodos()
    {
        $stmt = $this->conn->prepare('SELECT id, nome, descricao, telefone, email FROM ' . $this->tableName . ' ORDER BY id ASC');
        $stmt->execute();

        $enderecos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $enderecos[] = new Endereco($row['id'], $row['nome'], $row['descricao'], $row['telefone'], $row['email']);
        }

        return $enderecos;
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
        $stmt = $this->conn->prepare('SELECT id, nome, descricao, telefone, email FROM ' . $this->tableName . ' ORDER BY id ASC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $enderecos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $enderecos[] = new Endereco($row['id'], $row['nome'], $row['descricao'], $row['telefone'], $row['email']);
        }

        return $enderecos;
    }

    public function buscaPorNomePaginado($nome, $limit, $offset)
    {
        $stmt = $this->conn->prepare('SELECT id, nome, descricao, telefone, email FROM ' . $this->tableName . ' WHERE nome LIKE :nome ORDER BY nome LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $enderecos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $enderecos[] = new Endereco($row['id'], $row['nome'], $row['descricao'], $row['telefone'], $row['email']);
        }

        return $enderecos;
    }
}