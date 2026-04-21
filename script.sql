CREATE TABLE IF NOT EXISTS usuario (
id INT GENERATED AS IDENTITY PRIMARY KEY,
login VARCHAR(30) NOT NULL UNIQUE,
senha VARCHAR(255) NOT NULL,
nome VARCHAR(255) NOT NULL,
role VARCHAR(20) NOT NULL DEFAULT 'INTERNO'
);

INSERT INTO usuario(login, senha, nome, role) VALUES ('arthur','202cb962ac59075b964b07152d234b70','arthur','INTERNO'); -- arthur:123
INSERT INTO usuario(login, senha, nome, role) VALUES ('vinicius','202cb962ac59075b964b07152d234b70','vinicius','INTERNO'); -- vinicius:123

CREATE TABLE IF NOT EXISTS endereco (
id INT GENERATED AS IDENTITY PRIMARY KEY,
nome VARCHAR(255) NOT NULL,
descricao TEXT,
telefone VARCHAR(30),
email VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS fornecedor (
id INT GENERATED AS IDENTITY PRIMARY KEY,
nome VARCHAR(255) NOT NULL,
descricao TEXT,
telefone VARCHAR(30),
email VARCHAR(255),
endereco_id INT NULL,
CONSTRAINT fk_fornecedor_endereco FOREIGN KEY (endereco_id) REFERENCES endereco(id)
);

CREATE TABLE IF NOT EXISTS estoque (
id INT GENERATED AS IDENTITY PRIMARY KEY,
quantidade INT NOT NULL DEFAULT 0,
preco DECIMAL(10,2) NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS produto (
id INT GENERATED AS IDENTITY PRIMARY KEY,
nome VARCHAR(255) NOT NULL,
descricao TEXT,
foto LONGBLOB NULL,
fornecedor_id INT NOT NULL,
estoque_id INT NOT NULL,
CONSTRAINT fk_produto_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES fornecedor(id),
CONSTRAINT fk_produto_estoque FOREIGN KEY (estoque_id) REFERENCES estoque(id)
);

INSERT INTO endereco (nome, descricao, telefone, email) VALUES
('Centro', 'Rua Principal, 100', '(54) 1111-1111', 'contato@fornecedor1.com'),
('Bairro Industrial', 'Avenida 2, 200', '(54) 2222-2222', 'contato@fornecedor2.com');

INSERT INTO fornecedor (nome, descricao, telefone, email, endereco_id) VALUES
('Fornecedor Sul', 'Fornecedor de eletrônicos', '(54) 3333-3333', 'vendas@sul.com', 1),
('Fornecedor Norte', 'Fornecedor de acessórios', '(54) 4444-4444', 'vendas@norte.com', 2);

INSERT INTO estoque (quantidade, preco) VALUES
(15, 1999.90),
(8, 299.90),
(0, 129.90);

INSERT INTO produto (nome, descricao, foto, fornecedor_id, estoque_id) VALUES
('Notebook Pro 14', 'Notebook para trabalho e estudo', NULL, 1, 1),
('Mouse Sem Fio', 'Mouse ergonômico com conexão 2.4G', NULL, 2, 2),
('Teclado Mecânico', 'Modelo com switches táteis', NULL, 2, 3);
