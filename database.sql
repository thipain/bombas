CREATE DATABASE loja;
USE loja;

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(100),
    badge VARCHAR(50),
    icon VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criar tabela para gerenciar os slides
CREATE TABLE IF NOT EXISTS slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    subtitulo TEXT,
    texto_botao VARCHAR(100) DEFAULT 'Ver Ofertas',
    link_botao VARCHAR(255) DEFAULT '#produtos',
    imagem_url VARCHAR(255),
    ordem INT DEFAULT 0,
    ativo BOOLEAN DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir slides iniciais (exemplo)
INSERT INTO slides (titulo, subtitulo, texto_botao, link_botao, imagem_url, ordem, ativo) VALUES
('Ferramentas Profissionais', 'As melhores marcas com até 40% de desconto', 'Ver Ofertas', '#produtos', 'assets/images/slider/ferramentas.jpg', 1, 1),
('Bombas D''água em Promoção', 'Qualidade garantida e entrega rápida', 'Confira Agora', '#produtos', 'assets/images/slider/bombas.jpg', 2, 1),
('Compressores de Ar', 'Para todos os tipos de trabalho', 'Comprar Agora', '#produtos', 'assets/images/slider/compressores.jpg', 3, 1);

-- Adicionar campos para especificações e múltiplas imagens
ALTER TABLE produtos 
ADD COLUMN especificacoes TEXT,
ADD COLUMN marca VARCHAR(100),
ADD COLUMN modelo VARCHAR(100),
ADD COLUMN estoque INT DEFAULT 0,
ADD COLUMN peso DECIMAL(10,2),
ADD COLUMN dimensoes VARCHAR(100);

-- Criar tabela para múltiplas imagens do produto
CREATE TABLE IF NOT EXISTS produto_imagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    imagem_url VARCHAR(255) NOT NULL,
    ordem INT DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir imagens de exemplo (ajuste os IDs conforme seus produtos)
INSERT INTO produto_imagens (produto_id, imagem_url, ordem) VALUES
(1, 'assets/images/produtos/produto1-1.jpg', 1),
(1, 'assets/images/produtos/produto1-2.jpg', 2),
(1, 'assets/images/produtos/produto1-3.jpg', 3);