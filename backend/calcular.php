<?php
/**
 * calcular.php
 * Script isolado de backend para cálculo de fraquezas, resistências, imunidades e super efetivos.
 * Utiliza conexão PDO com MySQL conforme requisitos.
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bd.php';
require_once __DIR__ . '/tipos_dados.php';

// Recebe os parâmetros (via GET ou POST)
$tipo1 = trim($_GET['tipo1'] ?? $_POST['tipo1'] ?? '');
$tipo2 = trim($_GET['tipo2'] ?? $_POST['tipo2'] ?? '');

// Validação básica
if (empty($tipo1)) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Informe ao menos o Tipo 1 para efetuar o cálculo.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Se o usuário selecionou o mesmo tipo duas vezes, normaliza para tipo único
if ($tipo1 === $tipo2 || empty($tipo2)) {
    $tipo2 = null;
}

// Verifica se os tipos são válidos
if (!isset($tipos_pokemon[$tipo1]) || ($tipo2 !== null && !isset($tipos_pokemon[$tipo2]))) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Um ou ambos os tipos selecionados são inválidos.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$pdo = getConexao();
$usouBD = false;
$multiplicadoresDefesa = [];

// Tenta carregar as relações diretamente do MySQL via PDO
if ($pdo !== null) {
    try {
        $stmt = $pdo->prepare("
            SELECT tipo_atacante, multiplicador 
            FROM relacoes_tipo 
            WHERE tipo_defensor = :defensor
        ");

        // Busca multiplicadores para Tipo 1
        $stmt->execute([':defensor' => $tipo1]);
        $rows1 = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $rows2 = [];
        if ($tipo2 !== null) {
            $stmt->execute([':defensor' => $tipo2]);
            $rows2 = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        }

        if (!empty($rows1)) {
            $usouBD = true;
            foreach (array_keys($tipos_pokemon) as $atacante) {
                $m1 = isset($rows1[$atacante]) ? (float)$rows1[$atacante] : 1.0;
                $m2 = ($tipo2 !== null && isset($rows2[$atacante])) ? (float)$rows2[$atacante] : 1.0;
                $multiplicadoresDefesa[$atacante] = $m1 * $m2;
            }
        }
    } catch (PDOException $e) {
        // Fallback silencioso para memória em caso de tabela ainda não inicializada
        $usouBD = false;
    }
}

// Fallback para matriz em memória se banco de dados estiver offline
if (!$usouBD) {
    foreach (array_keys($tipos_pokemon) as $atacante) {
        $m1 = $efetividade_ataque[$atacante][$tipo1] ?? 1.0;
        $m2 = $tipo2 !== null ? ($efetividade_ataque[$atacante][$tipo2] ?? 1.0) : 1.0;
        $multiplicadoresDefesa[$atacante] = (float)($m1 * $m2);
    }
}

// Separação em categorias de defesa
$superFraquezas = [];   // 4x
$fraquezas = [];        // 2x
$neutros = [];          // 1x
$resistencias = [];     // 0.5x
$superResistencias = [];// 0.25x
$imunidades = [];       // 0x

foreach ($multiplicadoresDefesa as $tipoAtacante => $mult) {
    $info = [
        'tipo' => $tipoAtacante,
        'multiplicador' => $mult,
        'cor' => $tipos_pokemon[$tipoAtacante]['cor'],
        'icone' => $tipos_pokemon[$tipoAtacante]['icone']
    ];

    if ($mult >= 4.0) {
        $superFraquezas[] = $info;
    } elseif ($mult >= 2.0) {
        $fraquezas[] = $info;
    } elseif ($mult == 0.0) {
        $imunidades[] = $info;
    } elseif ($mult <= 0.25) {
        $superResistencias[] = $info;
    } elseif ($mult <= 0.5) {
        $resistencias[] = $info;
    } else {
        $neutros[] = $info;
    }
}

// Cálculo dos Super Efetivos no ATAQUE (STAB - Same Type Attack Bonus)
// Mostra quais tipos o Pokémon acerta com super efetividade (2x)
$superEfetivoAtaque = [];
$tiposOfensivos = [$tipo1];
if ($tipo2 !== null) {
    $tiposOfensivos[] = $tipo2;
}

foreach (array_keys($tipos_pokemon) as $alvoDefensor) {
    $atingeCom2x = false;
    $golpesEfetivos = [];

    foreach ($tiposOfensivos as $tipoAtk) {
        $multAtk = $efetividade_ataque[$tipoAtk][$alvoDefensor] ?? 1.0;
        if ($multAtk > 1.0) {
            $atingeCom2x = true;
            $golpesEfetivos[] = $tipoAtk;
        }
    }

    if ($atingeCom2x) {
        $superEfetivoAtaque[] = [
            'tipo' => $alvoDefensor,
            'golpes' => $golpesEfetivos,
            'cor' => $tipos_pokemon[$alvoDefensor]['cor'],
            'icone' => $tipos_pokemon[$alvoDefensor]['icone']
        ];
    }
}

// Salva na sessão nativa do PHP para persistência
$_SESSION['ultimo_calculo'] = [
    'tipo1' => $tipo1,
    'tipo2' => $tipo2,
    'data'  => date('Y-m-d H:i:s')
];

// Retorno JSON estruturado
echo json_encode([
    'sucesso' => true,
    'usou_banco' => $usouBD,
    'tipagem' => [
        'tipo1' => [
            'nome' => $tipo1,
            'cor' => $tipos_pokemon[$tipo1]['cor'],
            'icone' => $tipos_pokemon[$tipo1]['icone']
        ],
        'tipo2' => $tipo2 !== null ? [
            'nome' => $tipo2,
            'cor' => $tipos_pokemon[$tipo2]['cor'],
            'icone' => $tipos_pokemon[$tipo2]['icone']
        ] : null
    ],
    'defesa' => [
        'super_fraquezas' => $superFraquezas,      // 4x
        'fraquezas' => $fraquezas,                // 2x
        'resistencias' => $resistencias,          // 0.5x
        'super_resistencias' => $superResistencias,// 0.25x
        'imunidades' => $imunidades,              // 0x
        'neutros' => $neutros                     // 1x
    ],
    'ataque' => [
        'super_efetivo' => $superEfetivoAtaque    // 2x
    ]
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
