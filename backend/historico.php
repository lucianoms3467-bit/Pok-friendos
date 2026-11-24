<?php
/**
 * historico.php
 * Gerencia o histórico de pesquisas utilizando sessões nativas do PHP.
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$acao = $_GET['acao'] ?? $_POST['acao'] ?? 'listar';

if ($acao === 'limpar') {
    $_SESSION['historico_pokemons'] = [];
    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Histórico de pesquisas limpo com sucesso.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$historico = $_SESSION['historico_pokemons'] ?? [];

echo json_encode([
    'sucesso' => true,
    'historico' => $historico
], JSON_UNESCAPED_UNICODE);
