<?php

class UsuarioDAO extends ClasseDAO implements IUsuarioDao
{
    private $tableName = 'usuario';

    public function insere($usuario)
    {
        $query = 'INSERT INTO ' . $this->tableName . ' (login, senha, nome, role) VALUES (:login, :senha, :nome, :role)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':login', $usuario->getLogin());
        $stmt->bindValue(':senha', md5($usuario->getSenha()));
        $stmt->bindValue(':nome', $usuario->getNome());
        $stmt->bindValue(':role', $usuario->getRole() ?: 'INTERNO');

        return $stmt->execute();
    }

    public function altera(&$usuario)
    {
        $query = 'UPDATE ' . $this->tableName . ' SET login = :login, nome = :nome, role = :role';
        if ($usuario->getSenha() !== null && $usuario->getSenha() !== '') {
            $query .= ', senha = :senha';
        }
        $query .= ' WHERE id = :id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':login', $usuario->getLogin());
        $stmt->bindValue(':nome', $usuario->getNome());
        $stmt->bindValue(':role', $usuario->getRole() ?: 'INTERNO');

        if ($usuario->getSenha() !== null && $usuario->getSenha() !== '') {
            $stmt->bindValue(':senha', md5($usuario->getSenha()));
        }

        $stmt->bindValue(':id', $usuario->getId());

        return $stmt->execute();
    }

    public function remove($usuario)
    {
        return $this->removePorId($usuario->getId());
    }

    public function removePorId($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM ' . $this->tableName . ' WHERE id = :id');
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function buscaPorId($id)
    {
        $stmt = $this->conn->prepare('SELECT id, login, senha, nome, role FROM ' . $this->tableName . ' WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Usuario($row['id'], $row['login'], $row['senha'], $row['nome'], $row['role'] ?? 'INTERNO') : null;
    }

    public function buscaPorNome($nome)
    {
        $stmt = $this->conn->prepare('SELECT id, login, senha, nome, role FROM ' . $this->tableName . ' WHERE nome LIKE :nome ORDER BY nome');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();

        $usuarios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario($row['id'], $row['login'], $row['senha'], $row['nome'], $row['role'] ?? 'INTERNO');
        }

        return $usuarios;
    }

    public function buscaPorLogin($login)
    {
        $stmt = $this->conn->prepare('SELECT id, login, senha, nome, role FROM ' . $this->tableName . ' WHERE login = :login LIMIT 1');
        $stmt->bindValue(':login', $login);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Usuario($row['id'], $row['login'], $row['senha'], $row['nome'], $row['role'] ?? 'INTERNO') : null;
    }

    public function buscaTodos()
    {
        $stmt = $this->conn->prepare('SELECT id, login, senha, nome, role FROM ' . $this->tableName . ' ORDER BY id ASC');
        $stmt->execute();

        $usuarios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario($row['id'], $row['login'], $row['senha'], $row['nome'], $row['role'] ?? 'INTERNO');
        }

        return $usuarios;
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
        $stmt = $this->conn->prepare('SELECT id, login, senha, nome, role FROM ' . $this->tableName . ' ORDER BY id ASC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $usuarios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario($row['id'], $row['login'], $row['senha'], $row['nome'], $row['role'] ?? 'INTERNO');
        }

        return $usuarios;
    }

    public function buscaPorNomePaginado($nome, $limit, $offset)
    {
        $stmt = $this->conn->prepare('SELECT id, login, senha, nome, role FROM ' . $this->tableName . ' WHERE nome LIKE :nome ORDER BY nome LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $usuarios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario($row['id'], $row['login'], $row['senha'], $row['nome'], $row['role'] ?? 'INTERNO');
        }

        return $usuarios;
    }
}