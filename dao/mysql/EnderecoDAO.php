<?php

class EnderecoDAO extends ClasseDAO implements IEnderecoDao
{
    private $tableName = 'endereco';

    public function buscaPorNome($nome)
    {
        $stmt = $this->conn->prepare('SELECT id, rua, numero, complemento, bairro, cep, cidade, estado FROM ' . $this->tableName . ' WHERE rua LIKE :nome ORDER BY rua');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();

        $enderecos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $enderecos[] = new Endereco($row['id'], $row['rua'], $row['numero'], $row['complemento'], $row['bairro'], $row['cep'], $row['cidade'], $row['estado']);
        }

        return $enderecos;
    }

    public function insere($endereco)
    {
        $stmt = $this->conn->prepare('INSERT INTO ' . $this->tableName . ' (rua, numero, complemento, bairro, cep, cidade, estado) VALUES (:rua, :numero, :complemento, :bairro, :cep, :cidade, :estado)');
        $stmt->bindValue(':rua', $endereco->getRua());
        $stmt->bindValue(':numero', $endereco->getNumero());
        $stmt->bindValue(':complemento', $endereco->getComplemento());
        $stmt->bindValue(':bairro', $endereco->getBairro());
        $stmt->bindValue(':cep', $endereco->getCep());
        $stmt->bindValue(':cidade', $endereco->getCidade());
        $stmt->bindValue(':estado', $endereco->getEstado());

        $stmt->execute();
        
        return $this->conn->lastInsertId();
    }

    public function altera(&$endereco)
    {
        $stmt = $this->conn->prepare('UPDATE ' . $this->tableName . ' SET rua = :rua, numero = :numero, complemento = :complemento, bairro = :bairro, cep = :cep, cidade = :cidade, estado = :estado WHERE id = :id');
        $stmt->bindValue(':rua', $endereco->getRua());
        $stmt->bindValue(':numero', $endereco->getNumero());
        $stmt->bindValue(':complemento', $endereco->getComplemento());
        $stmt->bindValue(':bairro', $endereco->getBairro());
        $stmt->bindValue(':cep', $endereco->getCep());
        $stmt->bindValue(':cidade', $endereco->getCidade());
        $stmt->bindValue(':estado', $endereco->getEstado());
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
        $stmt = $this->conn->prepare('SELECT id, rua, numero, complemento, bairro, cep, cidade, estado FROM ' . $this->tableName . ' WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Endereco($row['id'], $row['rua'], $row['numero'], $row['complemento'], $row['bairro'], $row['cep'], $row['cidade'], $row['estado']) : null;
    }

    // ── Métodos refatorados (Buscando pela Rua) ──────────────────────────────────

    public function buscaPorRua($rua)
    {
        $stmt = $this->conn->prepare('SELECT id, rua, numero, complemento, bairro, cep, cidade, estado FROM ' . $this->tableName . ' WHERE rua LIKE :rua ORDER BY rua');
        $stmt->bindValue(':rua', '%' . $rua . '%');
        $stmt->execute();

        $enderecos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $enderecos[] = new Endereco($row['id'], $row['rua'], $row['numero'], $row['complemento'], $row['bairro'], $row['cep'], $row['cidade'], $row['estado']);
        }

        return $enderecos;
    }

    public function buscaTodos()
    {
        $stmt = $this->conn->prepare('SELECT id, rua, numero, complemento, bairro, cep, cidade, estado FROM ' . $this->tableName . ' ORDER BY id ASC');
        $stmt->execute();

        $enderecos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $enderecos[] = new Endereco($row['id'], $row['rua'], $row['numero'], $row['complemento'], $row['bairro'], $row['cep'], $row['cidade'], $row['estado']);
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

    public function contaPorRua($rua)
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE rua LIKE :rua');
        $stmt->bindValue(':rua', '%' . $rua . '%');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function buscaTodosPaginado($limit, $offset)
    {
        $stmt = $this->conn->prepare('SELECT id, rua, numero, complemento, bairro, cep, cidade, estado FROM ' . $this->tableName . ' ORDER BY id ASC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $enderecos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $enderecos[] = new Endereco($row['id'], $row['rua'], $row['numero'], $row['complemento'], $row['bairro'], $row['cep'], $row['cidade'], $row['estado']);
        }

        return $enderecos;
    }

    public function buscaPorRuaPaginado($rua, $limit, $offset)
    {
        $stmt = $this->conn->prepare('SELECT id, rua, numero, complemento, bairro, cep, cidade, estado FROM ' . $this->tableName . ' WHERE rua LIKE :rua ORDER BY rua LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':rua', '%' . $rua . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $enderecos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $enderecos[] = new Endereco($row['id'], $row['rua'], $row['numero'], $row['complemento'], $row['bairro'], $row['cep'], $row['cidade'], $row['estado']);
        }

        return $enderecos;
    }
}