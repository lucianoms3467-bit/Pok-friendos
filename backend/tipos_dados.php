<?php
/**
 * tipos_dados.php
 * Dados e matriz completa de efetividade de tipos Pokémon (Gerações 6 a 9).
 */

$tipos_pokemon = [
    'Normal'   => ['en' => 'normal',   'cor' => '#A8A878', 'bg' => '#6D6D4E', 'icone' => 'circle'],
    'Fogo'     => ['en' => 'fire',     'cor' => '#EE8130', 'bg' => '#9C531F', 'icone' => 'fire'],
    'Água'     => ['en' => 'water',    'cor' => '#6390F0', 'bg' => '#445E9C', 'icone' => 'droplet-fill'],
    'Grama'    => ['en' => 'grass',    'cor' => '#7AC74C', 'bg' => '#4E8234', 'icone' => 'tree-fill'],
    'Elétrico' => ['en' => 'electric', 'cor' => '#F7D02C', 'bg' => '#A1871F', 'icone' => 'lightning-charge-fill'],
    'Gelo'     => ['en' => 'ice',      'cor' => '#96D9D6', 'bg' => '#638D8D', 'icone' => 'snow'],
    'Lutador'  => ['en' => 'fighting', 'cor' => '#C22E28', 'bg' => '#7D1F1A', 'icone' => 'shield-slash-fill'],
    'Veneno'   => ['en' => 'poison',   'cor' => '#A33EA1', 'bg' => '#682A68', 'icone' => 'virus'],
    'Terra'    => ['en' => 'ground',   'cor' => '#E2BF65', 'bg' => '#927D44', 'icone' => 'gem'],
    'Voador'   => ['en' => 'flying',   'cor' => '#A98FF3', 'bg' => '#6D5E9C', 'icone' => 'wind'],
    'Psíquico' => ['en' => 'psychic',  'cor' => '#F95587', 'bg' => '#A13959', 'icone' => 'eye-fill'],
    'Inseto'   => ['en' => 'bug',      'cor' => '#A6B91A', 'bg' => '#6D7815', 'icone' => 'bug-fill'],
    'Pedra'    => ['en' => 'rock',     'cor' => '#B6A136', 'bg' => '#786B24', 'icone' => 'box-fill'],
    'Fantasma' => ['en' => 'ghost',    'cor' => '#735797', 'bg' => '#493963', 'icone' => 'moon-stars-fill'],
    'Dragão'   => ['en' => 'dragon',   'cor' => '#6F35FC', 'bg' => '#4924A1', 'icone' => 'stars'],
    'Noturno'  => ['en' => 'dark',     'cor' => '#705746', 'bg' => '#49392F', 'icone' => 'moon-fill'],
    'Aço'      => ['en' => 'steel',    'cor' => '#B7B7CE', 'bg' => '#787887', 'icone' => 'shield-fill'],
    'Fada'     => ['en' => 'fairy',    'cor' => '#D685AD', 'bg' => '#9B597B', 'icone' => 'heart-fill']
];

// Mapeamento em inglês para português
$traducao_en_pt = [
    'normal' => 'Normal',
    'fire' => 'Fogo',
    'water' => 'Água',
    'grass' => 'Grama',
    'electric' => 'Elétrico',
    'ice' => 'Gelo',
    'fighting' => 'Lutador',
    'poison' => 'Veneno',
    'ground' => 'Terra',
    'flying' => 'Voador',
    'psychic' => 'Psíquico',
    'bug' => 'Inseto',
    'rock' => 'Pedra',
    'ghost' => 'Fantasma',
    'dragon' => 'Dragão',
    'dark' => 'Noturno',
    'steel' => 'Aço',
    'fairy' => 'Fada'
];

/**
 * Matriz de multiplicadores de dano no ataque:
 * formato: $efetividade_ataque[atacante][defensor] = multiplicador
 * Padrão é 1.0 (neutro) se não especificado.
 */
$efetividade_ataque = [
    'Normal' => [
        'Pedra' => 0.5, 'Fantasma' => 0.0, 'Aço' => 0.5
    ],
    'Fogo' => [
        'Fogo' => 0.5, 'Água' => 0.5, 'Grama' => 2.0, 'Gelo' => 2.0,
        'Inseto' => 2.0, 'Pedra' => 0.5, 'Dragão' => 0.5, 'Aço' => 2.0
    ],
    'Água' => [
        'Fogo' => 2.0, 'Água' => 0.5, 'Grama' => 0.5, 'Terra' => 2.0,
        'Pedra' => 2.0, 'Dragão' => 0.5
    ],
    'Grama' => [
        'Fogo' => 0.5, 'Água' => 2.0, 'Grama' => 0.5, 'Veneno' => 0.5,
        'Terra' => 2.0, 'Voador' => 0.5, 'Inseto' => 0.5, 'Pedra' => 2.0,
        'Dragão' => 0.5, 'Aço' => 0.5
    ],
    'Elétrico' => [
        'Água' => 2.0, 'Elétrico' => 0.5, 'Grama' => 0.5, 'Terra' => 0.0,
        'Voador' => 2.0, 'Dragão' => 0.5
    ],
    'Gelo' => [
        'Fogo' => 0.5, 'Água' => 0.5, 'Grama' => 2.0, 'Gelo' => 0.5,
        'Terra' => 2.0, 'Voador' => 2.0, 'Dragão' => 2.0, 'Aço' => 0.5
    ],
    'Lutador' => [
        'Normal' => 2.0, 'Gelo' => 2.0, 'Veneno' => 0.5, 'Voador' => 0.5,
        'Psíquico' => 0.5, 'Inseto' => 0.5, 'Pedra' => 2.0, 'Fantasma' => 0.0,
        'Noturno' => 2.0, 'Aço' => 2.0, 'Fada' => 0.5
    ],
    'Veneno' => [
        'Grama' => 2.0, 'Veneno' => 0.5, 'Terra' => 0.5, 'Pedra' => 0.5,
        'Fantasma' => 0.5, 'Aço' => 0.0, 'Fada' => 2.0
    ],
    'Terra' => [
        'Fogo' => 2.0, 'Elétrico' => 2.0, 'Grama' => 0.5, 'Veneno' => 2.0,
        'Voador' => 0.0, 'Inseto' => 0.5, 'Pedra' => 2.0, 'Aço' => 2.0
    ],
    'Voador' => [
        'Elétrico' => 0.5, 'Grama' => 2.0, 'Lutador' => 2.0, 'Inseto' => 2.0,
        'Pedra' => 0.5, 'Aço' => 0.5
    ],
    'Psíquico' => [
        'Lutador' => 2.0, 'Veneno' => 2.0, 'Psíquico' => 0.5, 'Noturno' => 0.0,
        'Aço' => 0.5
    ],
    'Inseto' => [
        'Fogo' => 0.5, 'Grama' => 2.0, 'Lutador' => 0.5, 'Veneno' => 0.5,
        'Voador' => 0.5, 'Psíquico' => 2.0, 'Fantasma' => 0.5, 'Aço' => 0.5,
        'Fada' => 0.5, 'Noturno' => 2.0
    ],
    'Pedra' => [
        'Fogo' => 2.0, 'Gelo' => 2.0, 'Lutador' => 0.5, 'Terra' => 0.5,
        'Voador' => 2.0, 'Inseto' => 2.0, 'Aço' => 0.5
    ],
    'Fantasma' => [
        'Normal' => 0.0, 'Psíquico' => 2.0, 'Fantasma' => 2.0, 'Noturno' => 0.5
    ],
    'Dragão' => [
        'Dragão' => 2.0, 'Aço' => 0.5, 'Fada' => 0.0
    ],
    'Noturno' => [
        'Lutador' => 0.5, 'Psíquico' => 2.0, 'Fantasma' => 2.0, 'Noturno' => 0.5,
        'Fada' => 0.5
    ],
    'Aço' => [
        'Fogo' => 0.5, 'Água' => 0.5, 'Elétrico' => 0.5, 'Gelo' => 2.0,
        'Pedra' => 2.0, 'Aço' => 0.5, 'Fada' => 2.0
    ],
    'Fada' => [
        'Fogo' => 0.5, 'Lutador' => 2.0, 'Veneno' => 0.5, 'Dragão' => 2.0,
        'Noturno' => 2.0, 'Aço' => 0.5
    ]
];
