<?php
/**
 * bd.php
 * Conexão com banco de dados MySQL utilizando PDO para prevenção de SQL Injection.
 * Projeto: Calculadora de Tipagens Pokémon (Pok-friendos)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações do Banco de Dados MySQL
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'pokefriendos');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

/**
 * Obtém ou reutiliza a conexão PDO com o MySQL.
 * Retorna PDO ou null em caso de falha de conexão.
 *
 * @return PDO|null
 */
function getConexao(): ?PDO {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // Previne injeção real forçando prepared statements nativos
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Armazena erro na sessão para ser exibido via Toast/Modal na interface
        $_SESSION['db_status'] = 'offline';
        $_SESSION['db_error'] = $e->getMessage();
        return null;
    }
}

// Inicializa a conexão padrão para scripts que importam bd.php diretamente
$pdo = getConexao();
