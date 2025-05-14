-- Create database
CREATE DATABASE IF NOT EXISTS micro_erp;
USE micro_erp;

-- Products table
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Product variations table
CREATE TABLE IF NOT EXISTS variacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

-- Inventory table
CREATE TABLE IF NOT EXISTS estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT,
    variacao_id INT NULL,
    quantidade INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (variacao_id) REFERENCES variacoes(id) ON DELETE CASCADE
);

-- Coupons table
CREATE TABLE IF NOT EXISTS cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    desconto DECIMAL(10, 2) NOT NULL,
    tipo ENUM('percentual', 'valor') NOT NULL DEFAULT 'percentual',
    valor_minimo DECIMAL(10, 2) NULL,
    data_validade DATE NOT NULL,
    status BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_nome VARCHAR(255) NOT NULL,
    cliente_email VARCHAR(255) NOT NULL,
    cliente_telefone VARCHAR(20) NULL,
    endereco VARCHAR(255) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado VARCHAR(50) NOT NULL,
    cep VARCHAR(10) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    frete DECIMAL(10, 2) NOT NULL,
    desconto DECIMAL(10, 2) DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL,
    cupom_id INT NULL,
    status VARCHAR(20) DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cupom_id) REFERENCES cupons(id) ON DELETE SET NULL
);

-- Order items table
CREATE TABLE IF NOT EXISTS pedido_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    variacao_id INT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (variacao_id) REFERENCES variacoes(id) ON DELETE SET NULL
);

-- Insert products with and without variations
-- Produtos com variacoes
INSERT INTO produtos (id, nome, preco) VALUES 
(1, 'Camiseta Star Wars - Edicao Especial', 89.90),    -- Com variacoes (tamanhos e cores)
(2, 'Action Figure Mandalorian', 159.90),              -- Com variacoes (poses)
(3, 'Caneca Termica Harry Potter', 49.90);             -- Com variacoes (casas)

-- Produtos sem variacoes (estoque unico)
INSERT INTO produtos (id, nome, preco) VALUES 
(4, 'Box Manga Attack on Titan', 219.90),              -- Sem variacao
(5, 'Jogo de Tabuleiro RPG Deluxe', 299.90),          -- Sem variacao
(6, 'Poster Metal Vingadores', 45.90);                 -- Sem variacao

-- Insert variations for products that have them
INSERT INTO variacoes (id, produto_id, nome) VALUES
-- Variacoes da Camiseta Star Wars
(1, 1, 'P Preto'),
(2, 1, 'M Preto'),
(3, 1, 'G Preto'),
(4, 1, 'P Branco'),
(5, 1, 'M Branco'),
(6, 1, 'G Branco'),

-- Variacoes do Action Figure
(7, 2, 'Pose Batalha'),
(8, 2, 'Pose Normal'),

-- Variacoes da Caneca
(9, 3, 'Grifindor'),
(10, 3, 'Slytherin'),
(11, 3, 'Ravenclaw'),
(12, 3, 'Hufflepuff');

-- Insert inventory for products with variations
INSERT INTO estoque (produto_id, variacao_id, quantidade) VALUES
-- Estoque Camiseta Star Wars
(1, 1, 20),  -- P Preto
(1, 2, 25),  -- M Preto
(1, 3, 15),  -- G Preto
(1, 4, 20),  -- P Branco
(1, 5, 25),  -- M Branco
(1, 6, 15),  -- G Branco

-- Estoque Action Figure
(2, 7, 10),  -- Pose Batalha
(2, 8, 8),   -- Pose Normal

-- Estoque Canecas
(3, 9, 15),   -- Grifindor
(3, 10, 15),  -- Slytherin
(3, 11, 15),  -- Ravenclaw
(3, 12, 15);  -- Hufflepuff

-- Insert inventory for products without variations
INSERT INTO estoque (produto_id, variacao_id, quantidade) VALUES
(4, NULL, 50),  -- Box Manga
(5, NULL, 30),  -- Jogo RPG
(6, NULL, 100); -- Poster

-- Insert coupon examples
INSERT INTO cupons (codigo, desconto, tipo, valor_minimo, data_validade, status) VALUES
('GEEK10', 10.00, 'percentual', 50.00, '2025-12-31', TRUE),
('WELCOME20', 20.00, 'percentual', 100.00, '2025-12-31', TRUE),
('FRETE', 15.00, 'valor', 80.00, '2025-12-31', TRUE); 