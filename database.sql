CREATE DATABASE IF NOT EXISTS loja;

USE loja;

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL,
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
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- Inserir slides iniciais (exemplo)
INSERT INTO
    slides (
        titulo,
        subtitulo,
        texto_botao,
        link_botao,
        imagem_url,
        ordem,
        ativo
    )
VALUES (
        'Ferramentas Profissionais',
        'As melhores marcas com até 40% de desconto',
        'Ver Ofertas',
        '#produtos',
        'assets/images/slider/ferramentas.jpg',
        1,
        1
    ),
    (
        'Bombas D''Água em Promoção',
        'Qualidade garantida e entrega rápida',
        'Confira Agora',
        '#produtos',
        'assets/images/slider/bombas.jpg',
        2,
        1
    ),
    (
        'Compressores de Ar',
        'Para todos os tipos de trabalho',
        'Comprar Agora',
        '#produtos',
        'assets/images/slider/compressores.jpg',
        3,
        1
    );

-- Adicionar campos para especificações e múltiplas imagens
ALTER TABLE produtos
ADD COLUMN IF NOT EXISTS especificacoes TEXT,
ADD COLUMN IF NOT EXISTS marca VARCHAR(100),
ADD COLUMN IF NOT EXISTS modelo VARCHAR(100),
ADD COLUMN IF NOT EXISTS estoque INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS peso DECIMAL(10, 2),
ADD COLUMN IF NOT EXISTS dimensoes VARCHAR(100);

-- Criar tabela para múltiplas imagens do produto
CREATE TABLE IF NOT EXISTS produto_imagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    imagem_url VARCHAR(255) NOT NULL,
    ordem INT DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- Criar tabela de administradores
CREATE TABLE IF NOT EXISTS `administradores` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nome` varchar(100) NOT NULL,
    `usuario` varchar(50) NOT NULL UNIQUE,
    `senha` varchar(255) NOT NULL,
    `email` varchar(100) DEFAULT NULL,
    `ativo` tinyint(1) DEFAULT 1,
    `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
    `ultimo_acesso` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_usuario` (`usuario`),
    KEY `idx_ativo` (`ativo`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


INSERT INTO
    `administradores` (
        `nome`,
        `usuario`,
        `senha`,
        `email`,
        `ativo`
    )
VALUES (
        'Rodrigo',
        '51815514000136',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        NULL,
        1
    )
ON DUPLICATE KEY UPDATE
    senha = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    nome = 'Rodrigo';

