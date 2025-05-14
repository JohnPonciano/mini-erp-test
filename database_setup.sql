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

-- Insert geek-themed products with strategic prices
INSERT INTO produtos (nome, preco) VALUES 
('Camiseta Star Wars - Edição Especial', 89.90),    -- Frete R$15
('Action Figure Mandalorian', 159.90),              -- Frete R$15
('Caneca Térmica Harry Potter', 49.90),             -- Frete R$20
('Funko Pop Demon Slayer', 129.90),                 -- Frete R$15
('Box Mangá Attack on Titan', 219.90),              -- Frete Grátis
('Jogo de Tabuleiro RPG Deluxe', 299.90),          -- Frete Grátis
('Miniatura Goku Dragon Ball', 149.90),             -- Frete R$15
('Poster Metal Vingadores', 45.90),                 -- Frete R$20
('Kit Chaveiros Pokémon', 59.90),                   -- Frete R$15
('Luminária 3D Death Star', 179.90);                -- Frete R$15

-- Insert product variations
INSERT INTO variacoes (produto_id, nome) VALUES
(1, 'P Preto'),
(1, 'M Preto'),
(1, 'G Preto'),
(1, 'P Branco'),
(1, 'M Branco'),
(1, 'G Branco'),
(2, 'Beskar Armor'),
(2, 'Combat Mode'),
(3, 'Grifinória 500ml'),
(3, 'Sonserina 500ml'),
(3, 'Corvinal 500ml'),
(3, 'Lufa-Lufa 500ml'),
(4, 'Tanjiro'),
(4, 'Nezuko'),
(4, 'Zenitsu'),
(7, 'Super Sayajin'),
(7, 'Base Form'),
(7, 'Ultra Instinct');

-- Insert inventory data
INSERT INTO estoque (produto_id, variacao_id, quantidade) VALUES
(1, 1, 20),  -- Camiseta P Preto
(1, 2, 25),  -- Camiseta M Preto
(1, 3, 15),  -- Camiseta G Preto
(1, 4, 20),  -- Camiseta P Branco
(1, 5, 25),  -- Camiseta M Branco
(1, 6, 15),  -- Camiseta G Branco
(2, 7, 10),  -- Mandalorian Beskar
(2, 8, 8),   -- Mandalorian Combat
(3, 9, 15),  -- Caneca Grifinória
(3, 10, 15), -- Caneca Sonserina
(3, 11, 15), -- Caneca Corvinal
(3, 12, 15), -- Caneca Lufa-Lufa
(4, 13, 12), -- Funko Tanjiro
(4, 14, 10), -- Funko Nezuko
(4, 15, 8),  -- Funko Zenitsu
(7, 16, 10), -- Goku SSJ
(7, 17, 10), -- Goku Base
(7, 18, 5);  -- Goku UI

-- Insert coupon examples
INSERT INTO cupons (codigo, desconto, tipo, valor_minimo, data_validade, status) VALUES
('GEEK10', 10.00, 'percentual', 50.00, '2025-12-31', TRUE),
('WELCOME20', 20.00, 'percentual', 100.00, '2025-12-31', TRUE),
('FRETE', 15.00, 'valor', 80.00, '2025-12-31', TRUE); 