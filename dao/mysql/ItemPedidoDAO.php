<?php

class ItemPedidoDAO extends ClasseDAO implements IItemPedidoDao
{
    private $tableName = 'item_pedido';

    public function insere($itemPedido)
    {
        $stmt = $this->conn->prepare('INSERT INTO ' . $this->tableName . ' (quantidade, preco, pedido_id, produto_id) VALUES (:quantidade, :preco, :pedido_id, :produto_id)');
        $stmt->bindValue(':quantidade', $itemPedido->getQuantidade());
        $stmt->bindValue(':preco', $itemPedido->getPreco());
        $stmt->bindValue(':pedido_id', $itemPedido->getPedidoId());
        $stmt->bindValue(':produto_id', $itemPedido->getProdutoId());

        return $stmt->execute();
    }

    public function altera(&$itemPedido)
    {
        $stmt = $this->conn->prepare('UPDATE ' . $this->tableName . ' SET quantidade = :quantidade, preco = :preco, pedido_id = :pedido_id, produto_id = :produto_id WHERE id = :id');
        $stmt->bindValue(':quantidade', $itemPedido->getQuantidade());
        $stmt->bindValue(':preco', $itemPedido->getPreco());
        $stmt->bindValue(':pedido_id', $itemPedido->getPedidoId());
        $stmt->bindValue(':produto_id', $itemPedido->getProdutoId());
        $stmt->bindValue(':id', $itemPedido->getId());

        return $stmt->execute();
    }

    public function remove($itemPedido)
    {
        return $this->removePorId($itemPedido->getId());
    }

    public function removePorId($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM ' . $this->tableName . ' WHERE id = :id');
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function buscaPorId($id)
    {
        $stmt = $this->conn->prepare('SELECT id, quantidade, preco, pedido_id, produto_id FROM ' . $this->tableName . ' WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new ItemPedido($row['id'], $row['quantidade'], $row['preco'], $row['pedido_id'], $row['produto_id']) : null;
    }

    public function buscaPorNome($nome)
    {
        $stmt = $this->conn->prepare('SELECT id, quantidade, preco, pedido_id, produto_id FROM ' . $this->tableName . ' WHERE CAST(id AS CHAR) LIKE :nome ORDER BY id');
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();

        $itens = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $itens[] = new ItemPedido($row['id'], $row['quantidade'], $row['preco'], $row['pedido_id'], $row['produto_id']);
        }

        return $itens;
    }

    public function buscaTodos()
    {
        $stmt = $this->conn->prepare('SELECT id, quantidade, preco, pedido_id, produto_id FROM ' . $this->tableName . ' ORDER BY id ASC');
        $stmt->execute();

        $itens = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $itens[] = new ItemPedido($row['id'], $row['quantidade'], $row['preco'], $row['pedido_id'], $row['produto_id']);
        }

        return $itens;
    }
}
