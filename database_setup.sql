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

-- Insert geek-themed products
INSERT INTO produtos (nome, preco) VALUES 
('Camiseta Star Wars', 49.90),
('Action Figure Avengers', 129.90),
('Caneca Harry Potter', 39.90),
('Funko Pop Game of Thrones', 89.90),
('HQ Batman: O Cavaleiro das Trevas', 59.90),
('Jogo de Tabuleiro Dungeons & Dragons', 299.90),
('Miniatura Senhor dos Anéis', 149.90),
('Poster Vingadores: Ultimato', 29.90),
('Chaveiro Marvel', 19.90),
('Almofada Star Trek', 45.90);

-- Insert product variations
INSERT INTO variacoes (produto_id, nome) VALUES
(1, 'Tamanho P'),
(1, 'Tamanho M'),
(1, 'Tamanho G'),
(1, 'Tamanho GG'),
(2, 'Iron Man'),
(2, 'Captain America'),
(2, 'Thor'),
(3, 'Grifinória'),
(3, 'Sonserina'),
(3, 'Corvinal'),
(3, 'Lufa-Lufa'),
(4, 'Jon Snow'),
(4, 'Daenerys Targaryen'),
(4, 'Tyrion Lannister'),
(7, 'Gandalf'),
(7, 'Frodo'),
(7, 'Gollum');

-- Insert inventory data
INSERT INTO estoque (produto_id, variacao_id, quantidade) VALUES
(1, 1, 25), -- Camiseta Star Wars P
(1, 2, 30), -- Camiseta Star Wars M
(1, 3, 20), -- Camiseta Star Wars G
(1, 4, 15), -- Camiseta Star Wars GG
(2, 5, 10), -- Action Figure Iron Man
(2, 6, 8),  -- Action Figure Captain America
(2, 7, 12), -- Action Figure Thor
(3, 8, 18), -- Caneca Harry Potter Grifinória
(3, 9, 15), -- Caneca Harry Potter Sonserina
(3, 10, 12), -- Caneca Harry Potter Corvinal
(3, 11, 10), -- Caneca Harry Potter Lufa-Lufa
(4, 12, 6),  -- Funko Pop Jon Snow
(4, 13, 4),  -- Funko Pop Daenerys
(4, 14, 8),  -- Funko Pop Tyrion
(5, NULL, 20), -- HQ Batman (sem variação)
(6, NULL, 5),  -- Jogo D&D (sem variação)
(7, 15, 7),  -- Miniatura Gandalf
(7, 16, 9),  -- Miniatura Frodo
(7, 17, 5),  -- Miniatura Gollum
(8, NULL, 30), -- Poster (sem variação)
(9, NULL, 50), -- Chaveiro (sem variação)
(10, NULL, 15); -- Almofada (sem variação)

-- Insert coupon examples
INSERT INTO cupons (codigo, desconto, tipo, valor_minimo, data_validade, status) VALUES
('GEEK10', 10.00, 'percentual', 50.00, '2023-12-31', TRUE),
('WELCOME20', 20.00, 'percentual', 100.00, '2023-12-31', TRUE),
('FRETE', 15.00, 'valor', 80.00, '2023-12-31', TRUE); 