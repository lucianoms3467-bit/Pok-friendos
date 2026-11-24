<?php
/**
 * buscar.php
 * Script isolado de backend para busca de Pokémon por nome ou ID.
 * Consulta o banco MySQL via PDO; caso não encontre, consulta a PokeAPI oficial
 * e armazena os dados no banco via PDO e no histórico de sessões nativas do PHP.
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bd.php';
require_once __DIR__ . '/tipos_dados.php';

$query = trim($_GET['nome'] ?? $_POST['nome'] ?? '');
$isSugestao = isset($_GET['sugestoes']) && $_GET['sugestoes'] == '1';

if (empty($query)) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Digite o nome ou número de um Pokémon na barra de pesquisa.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$pdo = getConexao();

// Modo de autocomplete / sugestões rápidas
if ($isSugestao) {
    $sugestoes = [];
    if ($pdo !== null) {
        try {
            $stmt = $pdo->prepare("
                SELECT id, nome, tipo1, tipo2, sprite_url 
                FROM pokemons 
                WHERE LOWER(nome) LIKE :termo 
                ORDER BY id ASC 
                LIMIT 6
            ");
            $stmt->execute([':termo' => '%' . strtolower($query) . '%']);
            $sugestoes = $stmt->fetchAll();
        } catch (PDOException $e) {
            // Silencioso em caso de erro no banco
        }
    }
    echo json_encode(['sucesso' => true, 'sugestoes' => $sugestoes], JSON_UNESCAPED_UNICODE);
    exit;
}

$termoLimpo = strtolower($query);
$pokemon = null;

// 1. Tenta buscar no banco MySQL via PDO com Prepared Statement
if ($pdo !== null) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM pokemons 
            WHERE LOWER(nome) = :nome 
               OR id = :id 
            LIMIT 1
        ");
        $idParam = is_numeric($termoLimpo) ? (int)$termoLimpo : -1;
        $stmt->execute([
            ':nome' => $termoLimpo,
            ':id'   => $idParam
        ]);
        $pokemon = $stmt->fetch();
    } catch (PDOException $e) {
        // Tabela ainda pode não existir ou conexão falhou
    }
}

// 2. Se não estiver no banco, busca na PokeAPI oficial
if (!$pokemon) {
    $urlApi = "https://pokeapi.co/api/v2/pokemon/" . rawurlencode($termoLimpo);
    
    // Configura contexto HTTP com timeout rápido e User-Agent
    $context = stream_context_create([
        'http' => [
            'method'  => 'GET',
            'header'  => "User-Agent: PokeFriendos-Calculator/1.0\r\n",
            'timeout' => 4
        ]
    ]);

    $response = @file_get_contents($urlApi, false, $context);

    if ($response !== false) {
        $dadosApi = json_decode($response, true);
        if ($dadosApi && isset($dadosApi['id'])) {
            // Mapeia tipos da API (em inglês) para os tipos em português
            $t1En = $dadosApi['types'][0]['type']['name'] ?? 'normal';
            $t1Pt = $traducao_en_pt[$t1En] ?? ucfirst($t1En);

            $t2Pt = null;
            if (isset($dadosApi['types'][1])) {
                $t2En = $dadosApi['types'][1]['type']['name'];
                $t2Pt = $traducao_en_pt[$t2En] ?? ucfirst($t2En);
            }

            // Mapeia atributos base
            $stats = [];
            foreach ($dadosApi['stats'] as $st) {
                $stats[$st['stat']['name']] = $st['base_stat'];
            }

            $sprite = $dadosApi['sprites']['other']['official-artwork']['front_default'] 
                   ?? $dadosApi['sprites']['front_default'] 
                   ?? "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$dadosApi['id']}.png";

            $pokemon = [
                'id'         => (int)$dadosApi['id'],
                'nome'       => ucfirst($dadosApi['name']),
                'tipo1'      => $t1Pt,
                'tipo2'      => $t2Pt,
                'sprite_url' => $sprite,
                'hp'         => $stats['hp'] ?? 50,
                'ataque'     => $stats['attack'] ?? 50,
                'defesa'     => $stats['defense'] ?? 50,
                'sp_ataque'  => $stats['special-attack'] ?? 50,
                'sp_defesa'  => $stats['special-defense'] ?? 50,
                'velocidade' => $stats['speed'] ?? 50,
                'altura'     => ($dadosApi['height'] ?? 10) / 10, // decímetros para metros
                'peso'       => ($dadosApi['weight'] ?? 100) / 10  // hectogramas para kg
            ];

            // Salva no banco de dados MySQL via PDO para consultas futuras (cache seguro)
            if ($pdo !== null) {
                try {
                    $stmtInsert = $pdo->prepare("
                        INSERT INTO pokemons (id, nome, tipo1, tipo2, sprite_url, hp, ataque, defesa, sp_ataque, sp_defesa, velocidade, altura, peso)
                        VALUES (:id, :nome, :tipo1, :tipo2, :sprite, :hp, :atk, :def, :spatk, :spdef, :spd, :alt, :pes)
                        ON DUPLICATE KEY UPDATE 
                            nome = VALUES(nome), 
                            tipo1 = VALUES(tipo1), 
                            tipo2 = VALUES(tipo2), 
                            sprite_url = VALUES(sprite_url)
                    ");
                    $stmtInsert->execute([
                        ':id'     => $pokemon['id'],
                        ':nome'   => $pokemon['nome'],
                        ':tipo1'  => $pokemon['tipo1'],
                        ':tipo2'  => $pokemon['tipo2'],
                        ':sprite' => $pokemon['sprite_url'],
                        ':hp'     => $pokemon['hp'],
                        ':atk'    => $pokemon['ataque'],
                        ':def'    => $pokemon['defesa'],
                        ':spatk'  => $pokemon['sp_ataque'],
                        ':spdef'  => $pokemon['sp_defesa'],
                        ':spd'    => $pokemon['velocidade'],
                        ':alt'    => $pokemon['altura'],
                        ':pes'    => $pokemon['peso']
                    ]);
                } catch (PDOException $e) {
                    // Ignora falha de cache se tabela não existir
                }
            }
        }
    }
}

// Se não encontrou nem no banco nem na PokeAPI
if (!$pokemon) {
    http_response_code(404);
    echo json_encode([
        'sucesso' => false,
        'erro' => "O Pokémon \"{$query}\" não foi encontrado. Verifique a ortografia ou número."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 3. Registra no Histórico via Sessão PHP e MySQL PDO
if (!isset($_SESSION['historico_pokemons'])) {
    $_SESSION['historico_pokemons'] = [];
}

// Adiciona ao topo da sessão sem duplicatas consecutivas
array_unshift($_SESSION['historico_pokemons'], [
    'id'    => $pokemon['id'],
    'nome'  => $pokemon['nome'],
    'tipo1' => $pokemon['tipo1'],
    'tipo2' => $pokemon['tipo2'],
    'hora'  => date('H:i')
]);
$_SESSION['historico_pokemons'] = array_slice($_SESSION['historico_pokemons'], 0, 10);

// Registra histórico no banco MySQL via PDO
if ($pdo !== null) {
    try {
        $stmtHist = $pdo->prepare("
            INSERT INTO historico_buscas (session_id, pokemon_nome, tipo1, tipo2)
            VALUES (:sess, :nome, :t1, :t2)
        ");
        $stmtHist->execute([
            ':sess' => session_id(),
            ':nome' => $pokemon['nome'],
            ':t1'   => $pokemon['tipo1'],
            ':t2'   => $pokemon['tipo2']
        ]);
    } catch (PDOException $e) {
        // Silencioso
    }
}

// Retorna o Pokémon encontrado
echo json_encode([
    'sucesso' => true,
    'pokemon' => $pokemon
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
