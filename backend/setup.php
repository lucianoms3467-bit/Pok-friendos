<?php
/**
 * setup.php
 * Script automatizado para criação do banco de dados e tabelas no MySQL usando PDO.
 * Pode ser chamado via navegador ou requisição AJAX.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/tipos_dados.php';

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'pokefriendos';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

try {
    // 1. Conexão inicial sem selecionar banco para criá-lo se necessário
    $dsnSemBanco = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdoRoot = new PDO($dsnSemBanco, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Cria banco de dados
    $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

    // 2. Conexão direta com o banco criado
    $dsnComBanco = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsnComBanco, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // 3. Criação das tabelas
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `tipos` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `nome` VARCHAR(30) NOT NULL UNIQUE,
            `nome_en` VARCHAR(30) NOT NULL UNIQUE,
            `cor` VARCHAR(10) NOT NULL,
            `icone` VARCHAR(50) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS `relacoes_tipo` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `tipo_atacante` VARCHAR(30) NOT NULL,
            `tipo_defensor` VARCHAR(30) NOT NULL,
            `multiplicador` DECIMAL(3, 2) NOT NULL DEFAULT 1.00,
            INDEX `idx_atacante` (`tipo_atacante`),
            INDEX `idx_defensor` (`tipo_defensor`),
            UNIQUE KEY `unique_relacao` (`tipo_atacante`, `tipo_defensor`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

        CREATE TABLE IF NOT EXISTS `historico_buscas` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `session_id` VARCHAR(128) NOT NULL,
            `pokemon_nome` VARCHAR(100) NOT NULL,
            `tipo1` VARCHAR(30) NOT NULL,
            `tipo2` VARCHAR(30) NULL,
            `data_pesquisa` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_session` (`session_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 4. Popula tabela de tipos usando Prepared Statements
    $stmtTipo = $pdo->prepare("
        INSERT INTO `tipos` (`nome`, `nome_en`, `cor`, `icone`)
        VALUES (:nome, :nome_en, :cor, :icone)
        ON DUPLICATE KEY UPDATE `cor` = VALUES(`cor`), `icone` = VALUES(`icone`)
    ");

    foreach ($tipos_pokemon as $nomePt => $dados) {
        $stmtTipo->execute([
            ':nome'    => $nomePt,
            ':nome_en' => $dados['en'],
            ':cor'     => $dados['cor'],
            ':icone'   => $dados['icone']
        ]);
    }

    // 5. Popula matriz de relações entre tipos (18 x 18)
    $stmtRelacao = $pdo->prepare("
        INSERT INTO `relacoes_tipo` (`tipo_atacante`, `tipo_defensor`, `multiplicador`)
        VALUES (:atacante, :defensor, :multiplicador)
        ON DUPLICATE KEY UPDATE `multiplicador` = VALUES(`multiplicador`)
    ");

    $nomesTipos = array_keys($tipos_pokemon);
    $pdo->beginTransaction();
    foreach ($nomesTipos as $atacante) {
        foreach ($nomesTipos as $defensor) {
            $mult = 1.0;
            if (isset($efetividade_ataque[$atacante][$defensor])) {
                $mult = $efetividade_ataque[$atacante][$defensor];
            }
            $stmtRelacao->execute([
                ':atacante'      => $atacante,
                ':defensor'      => $defensor,
                ':multiplicador' => $mult
            ]);
        }
    }
    $pdo->commit();

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Banco de dados pokefriendos e tabelas configurados com sucesso via PDO!',
        'detalhes' => [
            'tipos_registrados' => count($tipos_pokemon),
            'relacoes_calculadas' => count($nomesTipos) * count($nomesTipos)
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Falha ao configurar MySQL: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
