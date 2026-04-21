<?php

class PedidoDAO extends ClasseDAO implements DAO
{
    private $tableName = 'pedido';

    public function insere($pedido)
    {
        $stmt = $this->conn->prepare('INSERT INTO ' . $this->tableName . ' (numero, data_pedido, data_entrega, situacao, cliente_id) VALUES (:numero, :data_pedido, :data_entrega, :situacao, :cliente_id)');
        $stmt->bindValue(':numero', $pedido->getNumero());
        $stmt->bindValue(':data_pedido', $pedido->getDataPedido());
        $stmt->bindValue(':data_entrega', $pedido->getDataEntrega());
        $stmt->bindValue(':situacao', $pedido->getSituacao());
        $stmt->bindValue(':cliente_id', $pedido->getClienteId());

        return $stmt->execute();
    }

    public function altera(&$pedido)
    {
        $stmt = $this->conn->prepare('UPDATE ' . $this->tableName . ' SET numero = :numero, data_pedido = :data_pedido, data_entrega = :data_entrega, situacao = :situacao, cliente_id = :cliente_id WHERE id = :id');
        $stmt->bindValue(':numero', $pedido->getNumero());
        $stmt->bindValue(':data_pedido', $pedido->getDataPedido());
        $stmt->bindValue(':data_entrega', $pedido->getDataEntrega());
        $stmt->bindValue(':situacao', $pedido->getSituacao());
        $stmt->bindValue(':cliente_id', $pedido->getClienteId());
        $stmt->bindValue(':id', $pedido->getId());

        return $stmt->execute();
    }

    public function remove($pedido)
    {
        return $this->removePorId($pedido->getId());
    }

    public function removePorId($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM ' . $this->tableName . ' WHERE id = :id');
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function buscaPorId($id)
    {
        $stmt = $this->conn->prepare('SELECT id, numero, data_pedido, data_entrega, situacao, cliente_id FROM ' . $this->tableName . ' WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Pedido($row['id'], $row['numero'], $row['data_pedido'], $row['data_entrega'], $row['situacao'], $row['cliente_id']) : null;
    }

    public function buscaPorNome($nome)
    {
        $stmt = $this->conn->prepare('SELECT id, numero, data_pedido, data_entrega, situacao, cliente_id FROM ' . $this->tableName . ' WHERE CAST(numero AS CHAR) LIKE :nome ORDER BY numero');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();

        $pedidos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = new Pedido($row['id'], $row['numero'], $row['data_pedido'], $row['data_entrega'], $row['situacao'], $row['cliente_id']);
        }

        return $pedidos;
    }

    public function buscaTodos()
    {
        $stmt = $this->conn->prepare('SELECT id, numero, data_pedido, data_entrega, situacao, cliente_id FROM ' . $this->tableName . ' ORDER BY id ASC');
        $stmt->execute();

        $pedidos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = new Pedido($row['id'], $row['numero'], $row['data_pedido'], $row['data_entrega'], $row['situacao'], $row['cliente_id']);
        }

        return $pedidos;
    }
}
