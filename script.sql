SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET CHARACTER SET utf8mb4;

-- 1. Tabela extra para o login 
CREATE TABLE IF NOT EXISTS usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(30) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'INTERNO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabela ENDEREÇO 
CREATE TABLE IF NOT EXISTS endereco (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rua VARCHAR(255) NOT NULL,
    numero VARCHAR(30) NOT NULL,
    complemento VARCHAR(255),
    bairro VARCHAR(255) NOT NULL,
    cep VARCHAR(30) NOT NULL,
    cidade VARCHAR(255) NOT NULL,
    estado VARCHAR(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabela CLIENTE 
CREATE TABLE IF NOT EXISTS cliente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(30),
    email VARCHAR(255),
    cartao_credito VARCHAR(20),
    endereco_id INT NOT NULL,
    CONSTRAINT fk_cliente_endereco FOREIGN KEY (endereco_id) REFERENCES endereco(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabela FORNECEDOR 
CREATE TABLE IF NOT EXISTS fornecedor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    telefone VARCHAR(30),
    email VARCHAR(255),
    endereco_id INT NULL,
    CONSTRAINT fk_fornecedor_endereco FOREIGN KEY (endereco_id) REFERENCES endereco(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabela PRODUTO 
CREATE TABLE IF NOT EXISTS produto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    foto LONGBLOB NULL,
    fornecedor_id INT NOT NULL,
    estoque_id INT NULL,
    CONSTRAINT fk_produto_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES fornecedor(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabela ESTOQUE 
CREATE TABLE IF NOT EXISTS estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quantidade INT NOT NULL DEFAULT 0,
    preco DECIMAL(10,2) NOT NULL DEFAULT 0,
    produto_id INT NOT NULL,
    CONSTRAINT fk_estoque_produto FOREIGN KEY (produto_id) REFERENCES produto(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da chave estrangeira de Produto -> Estoque (relação 0..1)
ALTER TABLE produto
    ADD CONSTRAINT fk_produto_estoque FOREIGN KEY (estoque_id) REFERENCES estoque(id) ON DELETE SET NULL;

-- 7. Tabela PEDIDO 
CREATE TABLE IF NOT EXISTS pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_pedido DATETIME NOT NULL,
    data_entrega DATE NULL,
    situacao VARCHAR(50) NOT NULL DEFAULT 'NOVO',
    cliente_id INT NOT NULL,
    CONSTRAINT fk_pedido_cliente FOREIGN KEY (cliente_id) REFERENCES cliente(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabela ITEM_PEDIDO 
CREATE TABLE IF NOT EXISTS item_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_item_pedido FOREIGN KEY (pedido_id) REFERENCES pedido(id) ON DELETE CASCADE,
    CONSTRAINT fk_item_produto FOREIGN KEY (produto_id) REFERENCES produto(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




INSERT INTO usuario(login, senha, nome, role) VALUES 
('arthur','202cb962ac59075b964b07152d234b70','Arthur Silva','INTERNO'), 
('vinicius','202cb962ac59075b964b07152d234b70','Vinicius Guilherme','INTERNO'); 

INSERT INTO endereco (rua, numero, complemento, bairro, cep, cidade, estado) VALUES
('Rua Principal', '100', 'Sala 2', 'Centro', '95000-000', 'Caxias do Sul', 'RS'),
('Avenida 2', '200', NULL, 'Bairro Industrial', '95000-111', 'Caxias do Sul', 'RS');

INSERT INTO fornecedor (nome, descricao, telefone, email, endereco_id) VALUES
('Fornecedor Sul', 'Fornecedor de eletrônicos', '(54) 3333-3333', 'vendas@sul.com', 1),
('Fornecedor Norte', 'Fornecedor de acessórios', '(54) 4444-4444', 'vendas@norte.com', 2);

INSERT INTO produto (nome, descricao, foto, fornecedor_id, estoque_id) VALUES
('Notebook Pro 14', 'Notebook para trabalho e estudo', NULL, 1, NULL),
('Mouse Sem Fio', 'Mouse ergonômico com conexão 2.4G', NULL, 2, NULL),
('Teclado Mecânico', 'Modelo com switches táteis', NULL, 2, NULL);

INSERT INTO estoque (quantidade, preco, produto_id) VALUES
(15, 1999.90, 1),
(8, 299.90, 2),
(0, 129.90, 3);

UPDATE produto p
INNER JOIN estoque e ON e.produto_id = p.id
SET p.estoque_id = e.id;
