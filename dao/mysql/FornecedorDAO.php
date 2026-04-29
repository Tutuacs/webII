<?php

class FornecedorDAO extends ClasseDAO implements IFornecedorDao
{
    private $tableName = 'fornecedor';

    private function normalizeNullable($value)
    {
        return ($value === null || $value === '') ? null : $value;
    }

    public function insere($fornecedor)
    {
        $stmt = $this->conn->prepare('INSERT INTO ' . $this->tableName . ' (nome, descricao, telefone, email, endereco_id) VALUES (:nome, :descricao, :telefone, :email, :endereco_id)');
        $stmt->bindValue(':nome', $fornecedor->getNome());
        $stmt->bindValue(':descricao', $fornecedor->getDescricao());
        $stmt->bindValue(':telefone', $fornecedor->getTelefone());
        $stmt->bindValue(':email', $fornecedor->getEmail());
        $stmt->bindValue(':endereco_id', $this->normalizeNullable($fornecedor->getEnderecoId()), $this->normalizeNullable($fornecedor->getEnderecoId()) === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function altera(&$fornecedor)
    {
        $stmt = $this->conn->prepare('UPDATE ' . $this->tableName . ' SET nome = :nome, descricao = :descricao, telefone = :telefone, email = :email, endereco_id = :endereco_id WHERE id = :id');
        $stmt->bindValue(':nome', $fornecedor->getNome());
        $stmt->bindValue(':descricao', $fornecedor->getDescricao());
        $stmt->bindValue(':telefone', $fornecedor->getTelefone());
        $stmt->bindValue(':email', $fornecedor->getEmail());
        $enderecoId = $this->normalizeNullable($fornecedor->getEnderecoId());
        $stmt->bindValue(':endereco_id', $enderecoId, $enderecoId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':id', $fornecedor->getId());

        return $stmt->execute();
    }

    public function remove($fornecedor)
    {
        return $this->removePorId($fornecedor->getId());
    }

    public function removePorId($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM ' . $this->tableName . ' WHERE id = :id');
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function buscaPorId($id)
    {
        $stmt = $this->conn->prepare('SELECT id, nome, descricao, telefone, email, endereco_id FROM ' . $this->tableName . ' WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Fornecedor($row['id'], $row['nome'], $row['descricao'], $row['telefone'], $row['email'], $row['endereco_id']) : null;
    }

    public function buscaPorNome($nome)
    {
        $stmt = $this->conn->prepare('SELECT id, nome, descricao, telefone, email, endereco_id FROM ' . $this->tableName . ' WHERE nome LIKE :nome ORDER BY nome');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();

        $fornecedores = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $fornecedores[] = new Fornecedor($row['id'], $row['nome'], $row['descricao'], $row['telefone'], $row['email'], $row['endereco_id']);
        }

        return $fornecedores;
    }

    public function buscaTodos()
    {
        $stmt = $this->conn->prepare('SELECT id, nome, descricao, telefone, email, endereco_id FROM ' . $this->tableName . ' ORDER BY id ASC');
        $stmt->execute();

        $fornecedores = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $fornecedores[] = new Fornecedor($row['id'], $row['nome'], $row['descricao'], $row['telefone'], $row['email'], $row['endereco_id']);
        }

        return $fornecedores;
    }
}
