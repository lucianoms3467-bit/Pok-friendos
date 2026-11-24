<?php
/**
 * index.php
 * Interface Principal da Calculadora de Tipagens Pokémon (Pok-friendos)
 * Atende aos requisitos:
 * - HTML5 estruturado
 * - PHP com sessões nativas
 * - Bootstrap 5 Mobile-First
 * - Notificações via Toast e Modais do Bootstrap
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/bd.php';
require_once __DIR__ . '/backend/tipos_dados.php';

include __DIR__ . '/views/header.php';
?>

<main class="container my-4">

  <!-- ==========================================
       SEÇÃO 1: BARRA DE PESQUISA (MOBILE-FIRST)
       ========================================== -->
  <section class="row justify-content-center mb-4">
    <div class="col-12 col-lg-10">
      <div class="pokedex-card p-3 p-md-4">
        <h1 class="h4 fw-bold text-dark mb-2 d-flex align-items-center gap-2">
          <i class="bi bi-search text-danger"></i> Pesquisar Pokémon
        </h1>
        <p class="text-muted small mb-3">
          Digite o nome ou número de qualquer Pokémon para obter automaticamente seus tipos e cálculo de combate.
        </p>

        <form id="formBuscaPokemon" class="position-relative">
          <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border">
            <span class="input-group-text bg-white border-0 ps-3 text-muted">
              <i class="bi bi-search"></i>
            </span>
            <input 
              type="text" 
              id="inputBusca" 
              class="form-control border-0 ps-2" 
              placeholder="Ex: Pikachu, Charizard, Lucario, Gengar ou 25..." 
              autocomplete="off" 
              required
            >
            <button class="btn btn-danger px-4 fw-semibold d-flex align-items-center gap-2" type="submit" id="btnBuscar">
              <span class="spinner-border spinner-border-sm d-none" id="searchSpinner" role="status" aria-hidden="true"></span>
              <span>Pesquisar</span>
            </button>
          </div>

          <!-- Menu de Autocomplete / Sugestões Dinâmicas -->
          <div id="sugestoesDropdown" class="sugestoes-dropdown d-none"></div>
        </form>

        <!-- Botões de Ação Rápida -->
        <div class="d-flex flex-wrap gap-2 mt-3 pt-2 border-top">
          <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 d-flex align-items-center gap-1" id="btnAleatorio">
            <i class="bi bi-dice-5-fill"></i> Pokémon Aleatório
          </button>
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-flex align-items-center gap-1" id="btnLimpar">
            <i class="bi bi-arrow-counterclockwise"></i> Reiniciar
          </button>
          <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-1 ms-auto" data-bs-toggle="collapse" data-bs-target="#seletorManualCollapse">
            <i class="bi bi-sliders"></i> Seleção Manual de Tipos
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================
       SEÇÃO 2: SELETOR MANUAL DE TIPOS (COLLAPSIBLE OU DIRETO)
       ========================================== -->
  <section class="row justify-content-center mb-4 collapse" id="seletorManualCollapse">
    <div class="col-12 col-lg-10">
      <div class="pokedex-card p-3 p-md-4 border border-primary-subtle bg-light">
        <h2 class="h5 fw-bold text-dark mb-2 d-flex align-items-center gap-2">
          <i class="bi bi-sliders text-primary"></i> Montar Combinação de Tipos Manualmente
        </h2>
        <p class="text-muted small mb-3">
          Escolha um tipo ou combine dois tipos para simular qualquer Pokémon ou nova criatura.
        </p>

        <form id="formCalculadoraTipos" class="row g-3 align-items-end">
          <div class="col-12 col-sm-5">
            <label for="selectTipo1" class="form-label fw-semibold small text-secondary">Tipo 1 (Primário) *</label>
            <select id="selectTipo1" class="form-select form-select-lg rounded-3 shadow-sm" required>
              <?php foreach ($tipos_pokemon as $nomeTipo => $dadosTipo): ?>
                <option value="<?= htmlspecialchars($nomeTipo) ?>" <?= $nomeTipo === 'Fogo' ? 'selected' : '' ?>>
                  <?= htmlspecialchars($nomeTipo) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12 col-sm-5">
            <label for="selectTipo2" class="form-label fw-semibold small text-secondary">Tipo 2 (Secundário - Opcional)</label>
            <select id="selectTipo2" class="form-select form-select-lg rounded-3 shadow-sm">
              <option value="">(Nenhum - Tipo Puro)</option>
              <?php foreach ($tipos_pokemon as $nomeTipo => $dadosTipo): ?>
                <option value="<?= htmlspecialchars($nomeTipo) ?>">
                  <?= htmlspecialchars($nomeTipo) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12 col-sm-2">
            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 fw-semibold shadow-sm">
              <i class="bi bi-calculator"></i> Calcular
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- ==========================================
       SEÇÃO 3: RESULTADOS (CARD DO POKÉMON + TABELAS DE EFETIVIDADE)
       ========================================== -->
  <section class="row justify-content-center g-4">
    
    <!-- Coluna Esquerda: Cartão Detalhado do Pokémon -->
    <div class="col-12 col-lg-4" id="colunaCardPokemon">
      <div class="pokedex-card p-4 text-center h-100" id="cardPokemonInfo">
        
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-bold fs-6" id="pokeNumero">#006</span>
          <span class="text-muted small"><i class="bi bi-shield-check"></i> Oficial</span>
        </div>

        <!-- Imagem do Pokémon -->
        <div class="pokemon-sprite-container mb-3">
          <img 
            id="pokeSprite" 
            src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/6.png" 
            alt="Pokémon" 
            class="pokemon-sprite"
          >
        </div>

        <!-- Nome e Tipagem -->
        <h2 class="h3 fw-bold text-dark mb-2 text-capitalize" id="pokeNome">Charizard</h2>
        <div class="d-flex justify-content-center gap-2 mb-4" id="pokeTipos">
          <span class="badge-tipo tipo-fogo"><i class="bi bi-fire"></i> Fogo</span>
          <span class="badge-tipo tipo-voador"><i class="bi bi-wind"></i> Voador</span>
        </div>

        <!-- Altura e Peso -->
        <div class="row g-2 mb-4 text-center">
          <div class="col-6">
            <div class="p-2 border rounded-3 bg-light">
              <div class="text-muted small">Altura</div>
              <strong class="text-dark" id="pokeAltura">1.7 m</strong>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 border rounded-3 bg-light">
              <div class="text-muted small">Peso</div>
              <strong class="text-dark" id="pokePeso">90.5 kg</strong>
            </div>
          </div>
        </div>

        <!-- Atributos Base -->
        <div class="text-start">
          <h6 class="fw-bold text-dark mb-3 small text-uppercase">Atributos Base (Stats)</h6>
          
          <div class="d-flex align-items-center mb-2">
            <span class="stat-label">HP</span>
            <span class="stat-val" id="statValHp">78</span>
            <div class="progress flex-grow-1 ms-2">
              <div class="progress-bar bg-success" id="statHp" style="width: 30%"></div>
            </div>
          </div>

          <div class="d-flex align-items-center mb-2">
            <span class="stat-label">Ataque</span>
            <span class="stat-val" id="statValAtk">84</span>
            <div class="progress flex-grow-1 ms-2">
              <div class="progress-bar bg-warning" id="statAtk" style="width: 44%"></div>
            </div>
          </div>

          <div class="d-flex align-items-center mb-2">
            <span class="stat-label">Defesa</span>
            <span class="stat-val" id="statValDef">78</span>
            <div class="progress flex-grow-1 ms-2">
              <div class="progress-bar bg-info" id="statDef" style="width: 34%"></div>
            </div>
          </div>

          <div class="d-flex align-items-center mb-2">
            <span class="stat-label">Sp. Atk</span>
            <span class="stat-val" id="statValSpAtk">109</span>
            <div class="progress flex-grow-1 ms-2">
              <div class="progress-bar bg-success" id="statSpAtk" style="width: 56%"></div>
            </div>
          </div>

          <div class="d-flex align-items-center mb-2">
            <span class="stat-label">Sp. Def</span>
            <span class="stat-val" id="statValSpDef">85</span>
            <div class="progress flex-grow-1 ms-2">
              <div class="progress-bar bg-info" id="statSpDef" style="width: 37%"></div>
            </div>
          </div>

          <div class="d-flex align-items-center">
            <span class="stat-label">Veloc.</span>
            <span class="stat-val" id="statValSpeed">100</span>
            <div class="progress flex-grow-1 ms-2">
              <div class="progress-bar bg-success" id="statSpeed" style="width: 55%"></div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Coluna Direita: Análise Completa de Fraquezas, Resistências e Super Efetivos -->
    <div class="col-12 col-lg-8">
      <div class="pokedex-card p-3 p-md-4 h-100" id="resultadoCalculos">
        
        <!-- Cabeçalho do Resultado -->
        <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-3">
          <div>
            <h3 class="h4 fw-bold text-dark m-0">Análise de Batalha & Efetividades</h3>
            <span class="text-muted small">Cálculo de fraquezas, resistências, imunidades e ataques</span>
          </div>
          <div class="d-flex gap-2 mt-2 mt-sm-0" id="topoTipagemBadges">
            <span class="badge-tipo badge-tipo-lg tipo-fogo"><i class="bi bi-fire"></i> Fogo</span>
            <span class="badge-tipo badge-tipo-lg tipo-voador"><i class="bi bi-wind"></i> Voador</span>
          </div>
        </div>

        <!-- Indicador de Carregamento -->
        <div id="loadingSpinner" class="text-center py-4 d-none">
          <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">Calculando...</span>
          </div>
          <p class="text-muted small mt-2 mb-0">Calculando matriz de tipos...</p>
        </div>

        <div class="row g-3">

          <!-- 1. Super Fraquezas (4x) -->
          <div class="col-12 col-md-6">
            <div class="efetividade-box h-100 border-danger-subtle bg-danger-subtle bg-opacity-10">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-danger d-flex align-items-center gap-1 small text-uppercase">
                  <i class="bi bi-exclamation-octagon-fill"></i> Super Fraquezas (4x Dano)
                </strong>
                <span class="badge bg-danger rounded-pill">4x</span>
              </div>
              <div id="listaSuperFraquezas" class="d-flex flex-wrap align-items-center">
                <!-- Preenchido via JS -->
              </div>
            </div>
          </div>

          <!-- 2. Fraquezas Normais (2x) -->
          <div class="col-12 col-md-6">
            <div class="efetividade-box h-100 border-warning-subtle bg-warning-subtle bg-opacity-10">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-warning-emphasis d-flex align-items-center gap-1 small text-uppercase">
                  <i class="bi bi-exclamation-triangle-fill"></i> Fraquezas (2x Dano)
                </strong>
                <span class="badge bg-warning text-dark rounded-pill">2x</span>
              </div>
              <div id="listaFraquezas" class="d-flex flex-wrap align-items-center">
                <!-- Preenchido via JS -->
              </div>
            </div>
          </div>

          <!-- 3. Resistências (0.5x) -->
          <div class="col-12 col-md-6">
            <div class="efetividade-box h-100 border-success-subtle bg-success-subtle bg-opacity-10">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-success d-flex align-items-center gap-1 small text-uppercase">
                  <i class="bi bi-shield-fill-check"></i> Resistências (½x Dano)
                </strong>
                <span class="badge bg-success rounded-pill">0.5x</span>
              </div>
              <div id="listaResistencias" class="d-flex flex-wrap align-items-center">
                <!-- Preenchido via JS -->
              </div>
            </div>
          </div>

          <!-- 4. Super Resistências (0.25x) -->
          <div class="col-12 col-md-6">
            <div class="efetividade-box h-100 border-info-subtle bg-info-subtle bg-opacity-10">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-info-emphasis d-flex align-items-center gap-1 small text-uppercase">
                  <i class="bi bi-shield-shaded"></i> Super Resistências (¼x Dano)
                </strong>
                <span class="badge bg-info text-dark rounded-pill">0.25x</span>
              </div>
              <div id="listaSuperResistencias" class="d-flex flex-wrap align-items-center">
                <!-- Preenchido via JS -->
              </div>
            </div>
          </div>

          <!-- 5. Imunidades (0x Dano) -->
          <div class="col-12 col-md-6">
            <div class="efetividade-box h-100 border-secondary-subtle bg-secondary-subtle bg-opacity-10">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-secondary-emphasis d-flex align-items-center gap-1 small text-uppercase">
                  <i class="bi bi-slash-circle-fill"></i> Imunidades (Sem Dano)
                </strong>
                <span class="badge bg-dark rounded-pill">0x</span>
              </div>
              <div id="listaImunidades" class="d-flex flex-wrap align-items-center">
                <!-- Preenchido via JS -->
              </div>
            </div>
          </div>

          <!-- 6. Super Efetivo no Ataque (2x STAB) -->
          <div class="col-12 col-md-6">
            <div class="efetividade-box h-100 border-primary-subtle bg-primary-subtle bg-opacity-10">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-primary d-flex align-items-center gap-1 small text-uppercase">
                  <i class="bi bi-lightning-charge-fill"></i> Super Efetivo no Ataque (STAB)
                </strong>
                <span class="badge bg-primary rounded-pill">2x Ataque</span>
              </div>
              <div id="listaSuperEfetivos" class="d-flex flex-wrap align-items-center">
                <!-- Preenchido via JS -->
              </div>
            </div>
          </div>

          <!-- 7. Dano Neutro (1x) -->
          <div class="col-12">
            <div class="efetividade-box">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-secondary d-flex align-items-center gap-1 small text-uppercase">
                  <i class="bi bi-circle"></i> Dano Normal Neutro (1x Dano)
                </strong>
                <span class="badge bg-secondary rounded-pill">1x</span>
              </div>
              <div id="listaNeutros" class="d-flex flex-wrap align-items-center">
                <!-- Preenchido via JS -->
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>

  </section>

  <!-- ==========================================
       SEÇÃO 4: HISTÓRICO DE BUSCAS RECENTES (SESSÃO NATIVA PHP)
       ========================================== -->
  <?php if (!empty($_SESSION['historico_pokemons'])): ?>
  <section class="row justify-content-center mt-4">
    <div class="col-12 col-lg-10">
      <div class="pokedex-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h3 class="h6 fw-bold text-dark m-0 d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-secondary"></i> Histórico Recente (Sessão PHP)
          </h3>
          <span class="badge bg-light text-secondary border rounded-pill">
            <?= count($_SESSION['historico_pokemons']) ?> pesquisas
          </span>
        </div>
        <div class="d-flex flex-wrap gap-2 pt-2 border-top">
          <?php foreach ($_SESSION['historico_pokemons'] as $item): ?>
            <button 
              type="button" 
              class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-flex align-items-center gap-1"
              onclick="buscarPokemon('<?= htmlspecialchars($item['nome'], ENT_QUOTES) ?>')"
            >
              <span><?= htmlspecialchars($item['nome']) ?></span>
              <span class="badge bg-secondary-subtle text-secondary small"><?= htmlspecialchars($item['tipo1']) ?></span>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

</main>

<?php include __DIR__ . '/views/footer.php'; ?>
