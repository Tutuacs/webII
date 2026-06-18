<?php

class EstoqueService
{
    private $factory;
    private $estoqueDao;
    private $produtoDao;

    public function __construct(DaoFactory $factory)
    {
        $this->factory = $factory;
        $this->estoqueDao = $factory->getEstoqueDao();
        $this->produtoDao = $factory->getProdutoDao();
    }

    public function listar()
    {
        return $this->estoqueDao->buscaTodos();
    }

    public function buscarPorId($id)
    {
        return $this->estoqueDao->buscaPorId($id);
    }

    public function buscarPorNome($nome)
    {
        return $this->estoqueDao->buscaPorNome($nome);
    }

    public function salvar(Estoque $estoque)
    {
        return $this->estoqueDao->insere($estoque);
    }

    public function salvarComProduto(Estoque $estoque)
    {
        $connection = $this->factory->getConnection();
        $connection->beginTransaction();

        try {
            $produto = $this->produtoDao->buscaPorId($estoque->getProdutoId());
            if ($produto === null) {
                throw new RuntimeException('Produto nao encontrado para vincular ao estoque.');
            }

            if ($produto->getEstoqueId()) {
                $estoque->setId($produto->getEstoqueId());
                $this->estoqueDao->altera($estoque);
            } else {
                $this->estoqueDao->insere($estoque);
                $produto->setEstoqueId($estoque->getId());
                $this->produtoDao->altera($produto);
            }

            $connection->commit();
            return true;
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $throwable;
        }
    }

    public function atualizar(Estoque $estoque)
    {
        return $this->estoqueDao->altera($estoque);
    }

    public function atualizarComProduto(Estoque $estoque)
    {
        $connection = $this->factory->getConnection();
        $connection->beginTransaction();

        try {
            $produto = $this->produtoDao->buscaPorId($estoque->getProdutoId());
            if ($produto === null) {
                throw new RuntimeException('Produto nao encontrado para atualizar o estoque.');
            }

            $this->estoqueDao->altera($estoque);

            if ((int) $produto->getEstoqueId() !== (int) $estoque->getId()) {
                $produto->setEstoqueId($estoque->getId());
                $this->produtoDao->altera($produto);
            }

            $connection->commit();
            return true;
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $throwable;
        }
    }

    public function excluir($estoque)
    {
        return $this->estoqueDao->remove($estoque);
    }

    // ── Paginação ──────────────────────────────────────────────────────────────

    public function listarPaginado($page, $perPage)
    {
        $offset = ($page - 1) * $perPage;
        return $this->estoqueDao->buscaTodosPaginado($perPage, $offset);
    }

    public function buscarPorNomePaginado($nome, $page, $perPage)
    {
        $offset = ($page - 1) * $perPage;
        return $this->estoqueDao->buscaPorNomePaginado($nome, $perPage, $offset);
    }

    public function contarTodos()
    {
        return $this->estoqueDao->contaTodos();
    }

    public function contarPorNome($nome)
    {
        return $this->estoqueDao->contaPorNome($nome);
    }
}