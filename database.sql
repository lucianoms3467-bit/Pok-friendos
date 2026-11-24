-- =========================================================
-- BANCO DE DADOS: Calculadora de Tipagens (Pok-friendos)
-- Arquitetura: MySQL com suporte a UTF-8 (utf8mb4)
-- =========================================================

CREATE DATABASE IF NOT EXISTS `pokefriendos` 
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `pokefriendos`;

-- 1. TABELA DE TIPOS
CREATE TABLE IF NOT EXISTS `tipos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(30) NOT NULL UNIQUE,
    `nome_en` VARCHAR(30) NOT NULL UNIQUE,
    `cor` VARCHAR(10) NOT NULL,
    `icone` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABELA DE RELAÇÕES ENTRE TIPOS (Efetividades)
CREATE TABLE IF NOT EXISTS `relacoes_tipo` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tipo_atacante` VARCHAR(30) NOT NULL,
    `tipo_defensor` VARCHAR(30) NOT NULL,
    `multiplicador` DECIMAL(3, 2) NOT NULL DEFAULT 1.00,
    INDEX `idx_atacante` (`tipo_atacante`),
    INDEX `idx_defensor` (`tipo_defensor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABELA DE POKÉMONS CADASTRADOS/CACHEADOS
CREATE TABLE IF NOT EXISTS `pokemons` (
    `id` INT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL UNIQUE,
    `tipo1` VARCHAR(30) NOT NULL,
    `tipo2` VARCHAR(30) NULL,
    `sprite_url` VARCHAR(255) NULL,
    `hp` INT DEFAULT 0,
    `ataque` INT DEFAULT 0,
    `defesa` INT DEFAULT 0,
    `sp_ataque` INT DEFAULT 0,
    `sp_defesa` INT DEFAULT 0,
    `velocidade` INT DEFAULT 0,
    `altura` DECIMAL(4, 2) DEFAULT 0,
    `peso` DECIMAL(5, 2) DEFAULT 0,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABELA DE HISTÓRICO DE BUSCAS
CREATE TABLE IF NOT EXISTS `historico_buscas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `session_id` VARCHAR(128) NOT NULL,
    `pokemon_nome` VARCHAR(100) NOT NULL,
    `tipo1` VARCHAR(30) NOT NULL,
    `tipo2` VARCHAR(30) NULL,
    `data_pesquisa` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- INSERÇÃO DOS 18 TIPOS OFICIAIS
INSERT INTO `tipos` (`nome`, `nome_en`, `cor`, `icone`) VALUES
('Normal',   'normal',   '#A8A878', 'circle'),
('Fogo',     'fire',     '#EE8130', 'fire'),
('Água',     'water',    '#6390F0', 'droplet-fill'),
('Grama',    'grass',    '#7AC74C', 'tree-fill'),
('Elétrico', 'electric', '#F7D02C', 'lightning-charge-fill'),
('Gelo',     'ice',      '#96D9D6', 'snow'),
('Lutador',  'fighting', '#C22E28', 'shield-slash-fill'),
('Veneno',   'poison',   '#A33EA1', 'virus'),
('Terra',    'ground',   '#E2BF65', 'gem'),
('Voador',   'flying',   '#A98FF3', 'wind'),
('Psíquico', 'psychic',  '#F95587', 'eye-fill'),
('Inseto',   'bug',      '#A6B91A', 'bug-fill'),
('Pedra',    'rock',     '#B6A136', 'box-fill'),
('Fantasma', 'ghost',    '#735797', 'moon-stars-fill'),
('Dragão',   'dragon',   '#6F35FC', 'stars'),
('Noturno',  'dark',     '#705746', 'moon-fill'),
('Aço',      'steel',    '#B7B7CE', 'shield-fill'),
('Fada',     'fairy',    '#D685AD', 'heart-fill')
ON DUPLICATE KEY UPDATE `cor` = VALUES(`cor`), `icone` = VALUES(`icone`);

-- POKÉMONS INICIAIS POPULARES DE EXEMPLO
INSERT INTO `pokemons` (`id`, `nome`, `tipo1`, `tipo2`, `sprite_url`, `hp`, `ataque`, `defesa`, `sp_ataque`, `sp_defesa`, `velocidade`, `altura`, `peso`) VALUES
(1, 'Bulbasaur', 'Grama', 'Veneno', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/1.png', 45, 49, 49, 65, 65, 45, 0.7, 6.9),
(4, 'Charmander', 'Fogo', NULL, 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/4.png', 39, 52, 43, 60, 50, 65, 0.6, 8.5),
(6, 'Charizard', 'Fogo', 'Voador', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/6.png', 78, 84, 78, 109, 85, 100, 1.7, 90.5),
(7, 'Squirtle', 'Água', NULL, 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/7.png', 44, 48, 65, 50, 64, 43, 0.5, 9.0),
(9, 'Blastoise', 'Água', NULL, 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/9.png', 79, 83, 100, 85, 105, 78, 1.6, 85.5),
(25, 'Pikachu', 'Elétrico', NULL, 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png', 35, 55, 40, 50, 50, 90, 0.4, 6.0),
(94, 'Gengar', 'Fantasma', 'Veneno', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/94.png', 60, 65, 60, 130, 75, 110, 1.5, 40.5),
(130, 'Gyarados', 'Água', 'Voador', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/130.png', 95, 125, 79, 60, 100, 81, 6.5, 235.0),
(149, 'Dragonite', 'Dragão', 'Voador', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/149.png', 91, 134, 95, 100, 100, 80, 2.2, 210.0),
(150, 'Mewtwo', 'Psíquico', NULL, 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/150.png', 106, 110, 90, 154, 90, 130, 2.0, 122.0),
(248, 'Tyranitar', 'Pedra', 'Noturno', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/248.png', 100, 134, 110, 95, 100, 61, 2.0, 202.0),
(282, 'Gardevoir', 'Psíquico', 'Fada', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/282.png', 68, 65, 65, 125, 115, 80, 1.6, 48.4),
(445, 'Garchomp', 'Dragão', 'Terra', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/445.png', 108, 130, 95, 80, 85, 102, 1.9, 95.0),
(448, 'Lucario', 'Lutador', 'Aço', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/448.png', 70, 110, 70, 115, 70, 90, 1.2, 54.0),
(658, 'Greninja', 'Água', 'Noturno', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/658.png', 72, 95, 67, 103, 71, 122, 1.5, 40.0),
(778, 'Mimikyu', 'Fantasma', 'Fada', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/778.png', 55, 90, 80, 50, 105, 96, 0.2, 0.7),
(823, 'Corviknight', 'Voador', 'Aço', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/823.png', 98, 87, 105, 53, 85, 67, 2.2, 75.0),
(959, 'Tinkaton', 'Fada', 'Aço', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/959.png', 85, 75, 77, 70, 105, 94, 0.7, 112.8)
ON DUPLICATE KEY UPDATE `nome` = VALUES(`nome`);
