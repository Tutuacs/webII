<?php

class ProdutoService
{
    private $factory;
    private $produtoDao;
    private $estoqueDao;

    public function __construct(DaoFactory $factory)
    {
        $this->factory = $factory;
        $this->produtoDao = $factory->getProdutoDao();
        $this->estoqueDao = $factory->getEstoqueDao();
    }

    public function listar()
    {
        return $this->produtoDao->buscaTodos();
    }

    public function buscarPorId($id)
    {
        return $this->produtoDao->buscaPorId($id);
    }

    public function buscarPorNome($nome)
    {
        return $this->produtoDao->buscaPorNome($nome);
    }

    public function salvar(Produto $produto)
    {
        return $this->produtoDao->insere($produto);
    }

    public function salvarComEstoque(Produto $produto, Estoque $estoque)
    {
        $connection = $this->factory->getConnection();
        $connection->beginTransaction();

        try {
            $produto->setEstoqueId(null);
            $this->produtoDao->insere($produto);

            $estoque->setProdutoId($produto->getId());
            $this->estoqueDao->insere($estoque);

            $produto->setEstoqueId($estoque->getId());
            $this->produtoDao->altera($produto);

            $connection->commit();
            return true;
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $throwable;
        }
    }

    public function atualizar(Produto $produto)
    {
        return $this->produtoDao->altera($produto);
    }

    public function atualizarComEstoque(Produto $produto, Estoque $estoque)
    {
        $connection = $this->factory->getConnection();
        $connection->beginTransaction();

        try {
            if ($estoque->getId()) {
                $estoque->setProdutoId($produto->getId());
                $this->estoqueDao->altera($estoque);
            } else {
                $estoque->setProdutoId($produto->getId());
                $this->estoqueDao->insere($estoque);
            }

            $produto->setEstoqueId($estoque->getId());
            $this->produtoDao->altera($produto);

            $connection->commit();
            return true;
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $throwable;
        }
    }

    public function excluir($produto)
    {
        return $this->produtoDao->remove($produto);
    }
}
