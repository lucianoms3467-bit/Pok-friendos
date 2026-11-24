/**
 * app.js
 * Lógica de Frontend para a Calculadora de Tipagens Pokémon (Pok-friendos)
 * Atende aos requisitos:
 * - Mensagens de erro claras na interface via TOAST
 * - Substituição de caixas de mensagens nativas por MODAL
 * - Cálculo de fraquezas, resistências, imunidades e super efetivos
 * - Busca de Pokémon e seleção manual de tipos
 */

// Matriz local de dados de apoio para execução autônoma (fallback client-side transparente)
const TIPOS_DATA = {
  'Normal':   { en: 'normal',   cor: '#A8A878', icone: 'circle' },
  'Fogo':     { en: 'fire',     cor: '#EE8130', icone: 'fire' },
  'Água':     { en: 'water',    cor: '#6390F0', icone: 'droplet-fill' },
  'Grama':    { en: 'grass',    cor: '#7AC74C', icone: 'tree-fill' },
  'Elétrico': { en: 'electric', cor: '#F7D02C', icone: 'lightning-charge-fill' },
  'Gelo':     { en: 'ice',      cor: '#96D9D6', icone: 'snow' },
  'Lutador':  { en: 'fighting', cor: '#C22E28', icone: 'shield-slash-fill' },
  'Veneno':   { en: 'poison',   cor: '#A33EA1', icone: 'virus' },
  'Terra':    { en: 'ground',   cor: '#E2BF65', icone: 'gem' },
  'Voador':   { en: 'flying',   cor: '#A98FF3', icone: 'wind' },
  'Psíquico': { en: 'psychic',  cor: '#F95587', icone: 'eye-fill' },
  'Inseto':   { en: 'bug',      cor: '#A6B91A', icone: 'bug-fill' },
  'Pedra':    { en: 'rock',     cor: '#B6A136', icone: 'box-fill' },
  'Fantasma': { en: 'ghost',    cor: '#735797', icone: 'moon-stars-fill' },
  'Dragão':   { en: 'dragon',   cor: '#6F35FC', icone: 'stars' },
  'Noturno':  { en: 'dark',     cor: '#705746', icone: 'moon-fill' },
  'Aço':      { en: 'steel',    cor: '#B7B7CE', icone: 'shield-fill' },
  'Fada':     { en: 'fairy',    cor: '#D685AD', icone: 'heart-fill' }
};

const TRADUCAO_EN_PT = {
  'normal': 'Normal', 'fire': 'Fogo', 'water': 'Água', 'grass': 'Grama',
  'electric': 'Elétrico', 'ice': 'Gelo', 'fighting': 'Lutador', 'poison': 'Veneno',
  'ground': 'Terra', 'flying': 'Voador', 'psychic': 'Psíquico', 'bug': 'Inseto',
  'rock': 'Pedra', 'ghost': 'Fantasma', 'dragon': 'Dragão', 'dark': 'Noturno',
  'steel': 'Aço', 'fairy': 'Fada'
};

const MATRIZ_ATAQUE = {
  'Normal':   { 'Pedra': 0.5, 'Fantasma': 0.0, 'Aço': 0.5 },
  'Fogo':     { 'Fogo': 0.5, 'Água': 0.5, 'Grama': 2.0, 'Gelo': 2.0, 'Inseto': 2.0, 'Pedra': 0.5, 'Dragão': 0.5, 'Aço': 2.0 },
  'Água':     { 'Fogo': 2.0, 'Água': 0.5, 'Grama': 0.5, 'Terra': 2.0, 'Pedra': 2.0, 'Dragão': 0.5 },
  'Grama':    { 'Fogo': 0.5, 'Água': 2.0, 'Grama': 0.5, 'Veneno': 0.5, 'Terra': 2.0, 'Voador': 0.5, 'Inseto': 0.5, 'Pedra': 2.0, 'Dragão': 0.5, 'Aço': 0.5 },
  'Elétrico': { 'Água': 2.0, 'Elétrico': 0.5, 'Grama': 0.5, 'Terra': 0.0, 'Voador': 2.0, 'Dragão': 0.5 },
  'Gelo':     { 'Fogo': 0.5, 'Água': 0.5, 'Grama': 2.0, 'Gelo': 0.5, 'Terra': 2.0, 'Voador': 2.0, 'Dragão': 2.0, 'Aço': 0.5 },
  'Lutador':  { 'Normal': 2.0, 'Gelo': 2.0, 'Veneno': 0.5, 'Voador': 0.5, 'Psíquico': 0.5, 'Inseto': 0.5, 'Pedra': 2.0, 'Fantasma': 0.0, 'Noturno': 2.0, 'Aço': 2.0, 'Fada': 0.5 },
  'Veneno':   { 'Grama': 2.0, 'Veneno': 0.5, 'Terra': 0.5, 'Pedra': 0.5, 'Fantasma': 0.5, 'Aço': 0.0, 'Fada': 2.0 },
  'Terra':    { 'Fogo': 2.0, 'Elétrico': 2.0, 'Grama': 0.5, 'Veneno': 2.0, 'Voador': 0.0, 'Inseto': 0.5, 'Pedra': 2.0, 'Aço': 2.0 },
  'Voador':   { 'Elétrico': 0.5, 'Grama': 2.0, 'Lutador': 2.0, 'Inseto': 2.0, 'Pedra': 0.5, 'Aço': 0.5 },
  'Psíquico': { 'Lutador': 2.0, 'Veneno': 2.0, 'Psíquico': 0.5, 'Noturno': 0.0, 'Aço': 0.5 },
  'Inseto':   { 'Fogo': 0.5, 'Grama': 2.0, 'Lutador': 0.5, 'Veneno': 0.5, 'Voador': 0.5, 'Psíquico': 2.0, 'Fantasma': 0.5, 'Aço': 0.5, 'Fada': 0.5, 'Noturno': 2.0 },
  'Pedra':    { 'Fogo': 2.0, 'Gelo': 2.0, 'Lutador': 0.5, 'Terra': 0.5, 'Voador': 2.0, 'Inseto': 2.0, 'Aço': 0.5 },
  'Fantasma': { 'Normal': 0.0, 'Psíquico': 2.0, 'Fantasma': 2.0, 'Noturno': 0.5 },
  'Dragão':   { 'Dragão': 2.0, 'Aço': 0.5, 'Fada': 0.0 },
  'Noturno':  { 'Lutador': 0.5, 'Psíquico': 2.0, 'Fantasma': 2.0, 'Noturno': 0.5, 'Fada': 0.5 },
  'Aço':      { 'Fogo': 0.5, 'Água': 0.5, 'Elétrico': 0.5, 'Gelo': 2.0, 'Pedra': 2.0, 'Aço': 0.5, 'Fada': 2.0 },
  'Fada':     { 'Fogo': 0.5, 'Lutador': 2.0, 'Veneno': 0.5, 'Dragão': 2.0, 'Noturno': 2.0, 'Aço': 0.5 }
};

// =========================================================
// SISTEMA DE MODAL (Substituto do alert e confirm nativos)
// =========================================================
let appModalInstance = null;
let modalConfirmCallback = null;

function inicializarModal() {
  const modalEl = document.getElementById('appModal');
  if (modalEl && typeof bootstrap !== 'undefined') {
    appModalInstance = new bootstrap.Modal(modalEl);

    const btnConfirm = document.getElementById('appModalConfirmBtn');
    if (btnConfirm) {
      btnConfirm.addEventListener('click', () => {
        if (typeof modalConfirmCallback === 'function') {
          modalConfirmCallback();
          modalConfirmCallback = null;
        }
      });
    }
  }
}

/**
 * Exibe uma mensagem em Modal Bootstrap seguro.
 * @param {string} titulo - Título do modal
 * @param {string} mensagem - Texto ou HTML da mensagem
 * @param {string} tipo - 'info', 'warning', 'danger', 'success'
 * @param {function} onConfirm - Callback opcional para confirmação
 */
function showAppModal(titulo, mensagem, tipo = 'info', onConfirm = null) {
  const modalTitle = document.getElementById('appModalTitle');
  const modalBody = document.getElementById('appModalMessage');
  const modalIcon = document.getElementById('appModalIcon');
  const btnCancel = document.getElementById('appModalCancelBtn');
  const btnConfirm = document.getElementById('appModalConfirmBtn');

  if (!modalTitle || !modalBody) {
    console.warn(titulo, mensagem);
    return;
  }

  modalTitle.textContent = titulo;
  modalBody.innerHTML = mensagem;

  // Estilização do ícone e botões com base no tipo
  modalIcon.className = 'bi fs-4';
  if (tipo === 'danger') {
    modalIcon.classList.add('bi-exclamation-triangle-fill', 'text-danger');
    btnConfirm.className = 'btn btn-danger px-4 rounded-pill';
  } else if (tipo === 'warning') {
    modalIcon.classList.add('bi-exclamation-circle-fill', 'text-warning');
    btnConfirm.className = 'btn btn-warning px-4 rounded-pill';
  } else if (tipo === 'success') {
    modalIcon.classList.add('bi-check-circle-fill', 'text-success');
    btnConfirm.className = 'btn btn-success px-4 rounded-pill';
  } else {
    modalIcon.classList.add('bi-info-circle-fill', 'text-primary');
    btnConfirm.className = 'btn btn-primary px-4 rounded-pill';
  }

  if (onConfirm) {
    btnCancel.style.display = 'inline-block';
    btnConfirm.textContent = 'Confirmar';
    modalConfirmCallback = onConfirm;
  } else {
    btnCancel.style.display = 'none';
    btnConfirm.textContent = 'Entendi';
    modalConfirmCallback = null;
  }

  if (appModalInstance) {
    appModalInstance.show();
  }
}

// Substitui a chamada nativa de alert do navegador para cumprir a regra global
window.alert = function (msg) {
  showAppModal('Aviso do Sistema', msg, 'info');
};

// =========================================================
// SISTEMA DE NOTIFICAÇÕES (TOAST)
// =========================================================
let appToastInstance = null;

function inicializarToast() {
  const toastEl = document.getElementById('appToast');
  if (toastEl && typeof bootstrap !== 'undefined') {
    appToastInstance = new bootstrap.Toast(toastEl, { delay: 4000 });
  }
}

/**
 * Exibe notificação Toast clara e estilizada na interface.
 * @param {string} mensagem - Texto da notificação
 * @param {string} tipo - 'success', 'danger', 'warning', 'info'
 */
function showToast(mensagem, tipo = 'danger') {
  const toastEl = document.getElementById('appToast');
  const toastMsg = document.getElementById('toastMessage');
  const toastIcon = document.getElementById('toastIcon');

  if (!toastEl || !toastMsg) return;

  toastMsg.textContent = mensagem;

  // Configura cores de fundo do Bootstrap
  toastEl.className = 'toast align-items-center text-white border-0 shadow-lg rounded-3';
  toastIcon.className = 'bi fs-5';

  if (tipo === 'danger' || tipo === 'erro') {
    toastEl.classList.add('bg-danger');
    toastIcon.classList.add('bi-x-circle-fill');
  } else if (tipo === 'success' || tipo === 'sucesso') {
    toastEl.classList.add('bg-success');
    toastIcon.classList.add('bi-check-circle-fill');
  } else if (tipo === 'warning') {
    toastEl.classList.add('bg-warning', 'text-dark');
    toastIcon.classList.add('bi-exclamation-triangle-fill');
  } else {
    toastEl.classList.add('bg-primary');
    toastIcon.classList.add('bi-info-circle-fill');
  }

  if (appToastInstance) {
    appToastInstance.show();
  }
}

// =========================================================
// LÓGICA DE CÁLCULO DE TIPAGEM (DEFESA E ATAQUE)
// =========================================================
/**
 * Realiza o cálculo de fraquezas, resistências, imunidades e super efetivos
 * Envia requisição para backend/calcular.php ou usa motor local se backend offline.
 */
async function calcularTipagens(tipo1, tipo2 = null) {
  if (!tipo1) {
    showToast('Por favor, selecione ao menos o Tipo 1.', 'warning');
    return;
  }

  if (tipo1 === tipo2) {
    tipo2 = null;
  }

  const spinner = document.getElementById('loadingSpinner');
  if (spinner) spinner.classList.remove('d-none');

  try {
    const url = `backend/calcular.php?tipo1=${encodeURIComponent(tipo1)}` + (tipo2 ? `&tipo2=${encodeURIComponent(tipo2)}` : '');
    const resp = await fetch(url);

    if (resp.ok) {
      const dados = await resp.json();
      if (dados.sucesso) {
        renderizarCalculos(dados);
        if (spinner) spinner.classList.add('d-none');
        return;
      }
    }
    throw new Error('Falha na resposta do backend');
  } catch (err) {
    // Fallback gracioso: calcula localmente mantendo 100% da precisão
    const dadosLocais = calcularTipagensLocalmente(tipo1, tipo2);
    renderizarCalculos(dadosLocais);
    if (spinner) spinner.classList.add('d-none');
  }
}

/**
 * Motor local para cálculo de tipagens (garante funcionamento mesmo sem servidor PHP ativo)
 */
function calcularTipagensLocalmente(tipo1, tipo2) {
  const todosTipos = Object.keys(TIPOS_DATA);
  const multDefesa = {};

  todosTipos.forEach(atacante => {
    const m1 = (MATRIZ_ATAQUE[atacante] && MATRIZ_ATAQUE[atacante][tipo1] !== undefined)
      ? MATRIZ_ATAQUE[atacante][tipo1] : 1.0;
    const m2 = (tipo2 && MATRIZ_ATAQUE[atacante] && MATRIZ_ATAQUE[atacante][tipo2] !== undefined)
      ? MATRIZ_ATAQUE[atacante][tipo2] : 1.0;
    multDefesa[atacante] = m1 * m2;
  });

  const superFraquezas = [];
  const fraquezas = [];
  const resistencias = [];
  const superResistencias = [];
  const imunidades = [];
  const neutros = [];

  todosTipos.forEach(tipo => {
    const mult = multDefesa[tipo];
    const info = {
      tipo: tipo,
      multiplicador: mult,
      cor: TIPOS_DATA[tipo].cor,
      icone: TIPOS_DATA[tipo].icone
    };

    if (mult >= 4.0) superFraquezas.push(info);
    else if (mult >= 2.0) fraquezas.push(info);
    else if (mult === 0.0) imunidades.push(info);
    else if (mult <= 0.25) superResistencias.push(info);
    else if (mult <= 0.5) resistencias.push(info);
    else neutros.push(info);
  });

  // Cálculo de ataque (Super Efetivo)
  const superEfetivoAtaque = [];
  const tiposAtk = [tipo1];
  if (tipo2) tiposAtk.push(tipo2);

  todosTipos.forEach(defensor => {
    let atinge = false;
    let golpes = [];
    tiposAtk.forEach(tAtk => {
      if (MATRIZ_ATAQUE[tAtk] && MATRIZ_ATAQUE[tAtk][defensor] > 1.0) {
        atinge = true;
        golpes.push(tAtk);
      }
    });

    if (atinge) {
      superEfetivoAtaque.push({
        tipo: defensor,
        golpes: golpes,
        cor: TIPOS_DATA[defensor].cor,
        icone: TIPOS_DATA[defensor].icone
      });
    }
  });

  return {
    sucesso: true,
    tipagem: {
      tipo1: { nome: tipo1, cor: TIPOS_DATA[tipo1].cor, icone: TIPOS_DATA[tipo1].icone },
      tipo2: tipo2 ? { nome: tipo2, cor: TIPOS_DATA[tipo2].cor, icone: TIPOS_DATA[tipo2].icone } : null
    },
    defesa: {
      super_fraquezas: superFraquezas,
      fraquezas: fraquezas,
      resistencias: resistencias,
      super_resistencias: superResistencias,
      imunidades: imunidades,
      neutros: neutros
    },
    ataque: {
      super_efetivo: superEfetivoAtaque
    }
  };
}

/**
 * Renderiza as seções de fraquezas, resistências, imunidades e super efetivos
 */
function renderizarCalculos(dados) {
  const container = document.getElementById('resultadoCalculos');
  if (!container) return;

  container.classList.remove('d-none');

  // 1. Super Fraquezas (4x)
  renderizarGrupoTipos('listaSuperFraquezas', dados.defesa.super_fraquezas, '4x', 'mult-4x');
  // 2. Fraquezas (2x)
  renderizarGrupoTipos('listaFraquezas', dados.defesa.fraquezas, '2x', 'mult-2x');
  // 3. Resistências (0.5x)
  renderizarGrupoTipos('listaResistencias', dados.defesa.resistencias, '½x', 'mult-05x');
  // 4. Super Resistências (0.25x)
  renderizarGrupoTipos('listaSuperResistencias', dados.defesa.super_resistencias, '¼x', 'mult-025x');
  // 5. Imunidades (0x)
  renderizarGrupoTipos('listaImunidades', dados.defesa.imunidades, '0x', 'mult-0x');
  // 6. Super Efetivo no Ataque (2x STAB)
  renderizarAtaqueEfetivo('listaSuperEfetivos', dados.ataque.super_efetivo);
  // 7. Neutros (1x)
  renderizarGrupoTipos('listaNeutros', dados.defesa.neutros, '1x', 'text-muted');

  // Atualiza badges do topo da calculadora
  atualizarBadgesTopo(dados.tipagem.tipo1, dados.tipagem.tipo2);

  // Rola suavemente até o resultado no mobile
  if (window.innerWidth < 768) {
    container.scrollIntoView({ behavior: 'smooth' });
  }
}

function renderizarGrupoTipos(elementId, lista, labelMult, classBadge) {
  const el = document.getElementById(elementId);
  if (!el) return;

  if (!lista || lista.length === 0) {
    el.innerHTML = '<span class="text-muted small fst-italic py-1 d-inline-block">Nenhum</span>';
    return;
  }

  el.innerHTML = lista.map(item => `
    <span class="badge-tipo badge-tipo-sm me-1 mb-2" style="background-color: ${item.cor};">
      <i class="bi bi-${item.icone}"></i>
      ${item.tipo}
      <span class="mult-tag ${classBadge} ms-1">${labelMult}</span>
    </span>
  `).join('');
}

function renderizarAtaqueEfetivo(elementId, lista) {
  const el = document.getElementById(elementId);
  if (!el) return;

  if (!lista || lista.length === 0) {
    el.innerHTML = '<span class="text-muted small fst-italic py-1 d-inline-block">Nenhum</span>';
    return;
  }

  el.innerHTML = lista.map(item => `
    <span class="badge-tipo badge-tipo-sm me-1 mb-2" style="background-color: ${item.cor};" title="Efetivo com: ${item.golpes.join(', ')}">
      <i class="bi bi-${item.icone}"></i>
      ${item.tipo}
      <span class="mult-tag mult-stab ms-1">2x</span>
    </span>
  `).join('');
}

function atualizarBadgesTopo(tipo1, tipo2) {
  const container = document.getElementById('topoTipagemBadges');
  if (!container) return;

  let html = `
    <span class="badge-tipo badge-tipo-lg" style="background-color: ${tipo1.cor};">
      <i class="bi bi-${tipo1.icone}"></i> ${tipo1.nome}
    </span>
  `;

  if (tipo2) {
    html += `
      <span class="badge-tipo badge-tipo-lg" style="background-color: ${tipo2.cor};">
        <i class="bi bi-${tipo2.icone}"></i> ${tipo2.nome}
      </span>
    `;
  }

  container.innerHTML = html;
}

// =========================================================
// BUSCA DE POKÉMON (BACKEND + POKEAPI FALLBACK)
// =========================================================
async function buscarPokemon(termo) {
  if (!termo || !termo.trim()) {
    showToast('Digite o nome ou número de um Pokémon.', 'warning');
    return;
  }

  const spinner = document.getElementById('searchSpinner');
  const btnBusca = document.getElementById('btnBuscar');
  if (spinner) spinner.classList.remove('d-none');
  if (btnBusca) btnBusca.disabled = true;

  try {
    const url = `backend/buscar.php?nome=${encodeURIComponent(termo.trim())}`;
    const resp = await fetch(url);
    const dados = await resp.json();

    if (resp.ok && dados.sucesso) {
      exibirPokemon(dados.pokemon);
      showToast(`Pokémon ${dados.pokemon.nome} carregado com sucesso!`, 'success');
      return;
    } else {
      throw new Error(dados.erro || 'Pokémon não encontrado');
    }
  } catch (err) {
    // Tenta consultar diretamente a PokeAPI pública caso o PHP esteja sem servidor
    try {
      const pokeData = await buscarPokeApiDireta(termo.trim().toLowerCase());
      exibirPokemon(pokeData);
      showToast(`Pokémon ${pokeData.nome} carregado!`, 'success');
    } catch (e2) {
      showToast(`Pokémon "${termo}" não encontrado. Verifique a grafia.`, 'danger');
      showAppModal(
        'Pokémon Não Encontrado',
        `Não encontramos nenhum Pokémon correspondente a "<strong>${termo}</strong>".<br><br>Dica: Tente pesquisar nomes como <em>Pikachu</em>, <em>Charizard</em>, <em>Gengar</em>, <em>Lucario</em> ou pelo número da Pokédex (ex: 25).`,
        'warning'
      );
    }
  } finally {
    if (spinner) spinner.classList.add('d-none');
    if (btnBusca) btnBusca.disabled = false;
  }
}

async function buscarPokeApiDireta(query) {
  const resp = await fetch(`https://pokeapi.co/api/v2/pokemon/${query}`);
  if (!resp.ok) throw new Error('Não encontrado');
  const data = await resp.json();

  const t1En = data.types[0].type.name;
  const t1Pt = TRADUCAO_EN_PT[t1En] || t1En;

  let t2Pt = null;
  if (data.types[1]) {
    const t2En = data.types[1].type.name;
    t2Pt = TRADUCAO_EN_PT[t2En] || t2En;
  }

  const statsMap = {};
  data.stats.forEach(st => {
    statsMap[st.stat.name] = st.base_stat;
  });

  const sprite = data.sprites.other?.['official-artwork']?.front_default 
              || data.sprites.front_default 
              || `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${data.id}.png`;

  return {
    id: data.id,
    nome: data.name.charAt(0).toUpperCase() + data.name.slice(1),
    tipo1: t1Pt,
    tipo2: t2Pt,
    sprite_url: sprite,
    hp: statsMap['hp'] || 50,
    ataque: statsMap['attack'] || 50,
    defesa: statsMap['defense'] || 50,
    sp_ataque: statsMap['special-attack'] || 50,
    sp_defesa: statsMap['special-defense'] || 50,
    velocidade: statsMap['speed'] || 50,
    altura: (data.height || 10) / 10,
    peso: (data.weight || 100) / 10
  };
}

/**
 * Exibe os dados do Pokémon pesquisado no Card Principal
 */
function exibirPokemon(p) {
  const cardPokemon = document.getElementById('cardPokemonInfo');
  if (!cardPokemon) return;

  cardPokemon.classList.remove('d-none');

  // Textos e Identificação
  document.getElementById('pokeNumero').textContent = '#' + String(p.id).padStart(3, '0');
  document.getElementById('pokeNome').textContent = p.nome;
  document.getElementById('pokeSprite').src = p.sprite_url;
  document.getElementById('pokeSprite').alt = p.nome;

  // Medidas
  document.getElementById('pokeAltura').textContent = (p.altura || 1.0) + ' m';
  document.getElementById('pokePeso').textContent = (p.peso || 10.0) + ' kg';

  // Badges de tipos do Pokémon
  const containerTipos = document.getElementById('pokeTipos');
  const cor1 = TIPOS_DATA[p.tipo1] ? TIPOS_DATA[p.tipo1].cor : '#777';
  const icone1 = TIPOS_DATA[p.tipo1] ? TIPOS_DATA[p.tipo1].icone : 'circle';

  let htmlTipos = `
    <span class="badge-tipo" style="background-color: ${cor1};">
      <i class="bi bi-${icone1}"></i> ${p.tipo1}
    </span>
  `;

  if (p.tipo2) {
    const cor2 = TIPOS_DATA[p.tipo2] ? TIPOS_DATA[p.tipo2].cor : '#777';
    const icone2 = TIPOS_DATA[p.tipo2] ? TIPOS_DATA[p.tipo2].icone : 'circle';
    htmlTipos += `
      <span class="badge-tipo" style="background-color: ${cor2};">
        <i class="bi bi-${icone2}"></i> ${p.tipo2}
      </span>
    `;
  }
  containerTipos.innerHTML = htmlTipos;

  // Estatísticas Base
  atualizarBarraStat('statHp', 'statValHp', p.hp, 255);
  atualizarBarraStat('statAtk', 'statValAtk', p.ataque, 190);
  atualizarBarraStat('statDef', 'statValDef', p.defesa, 230);
  atualizarBarraStat('statSpAtk', 'statValSpAtk', p.sp_ataque, 194);
  atualizarBarraStat('statSpDef', 'statValSpDef', p.sp_defesa, 230);
  atualizarBarraStat('statSpeed', 'statValSpeed', p.velocidade, 180);

  // Sincroniza os selects manuais de tipo
  const select1 = document.getElementById('selectTipo1');
  const select2 = document.getElementById('selectTipo2');
  if (select1) select1.value = p.tipo1;
  if (select2) select2.value = p.tipo2 || '';

  // Executa o cálculo para a tipagem do Pokémon pesquisado
  calcularTipagens(p.tipo1, p.tipo2);
}

function atualizarBarraStat(barId, valId, valor, max) {
  const bar = document.getElementById(barId);
  const val = document.getElementById(valId);
  if (!bar || !val) return;

  const pct = Math.min(100, Math.round((valor / max) * 100));
  bar.style.width = pct + '%';
  val.textContent = valor;

  // Cor baseada na pontuação
  bar.className = 'progress-bar';
  if (valor >= 100) {
    bar.classList.add('bg-success');
  } else if (valor >= 65) {
    bar.classList.add('bg-info');
  } else {
    bar.classList.add('bg-warning');
  }
}

// =========================================================
// AUTOCOMPLETE / SUGESTÕES RÁPIDAS
// =========================================================
let debounceTimer = null;

function configurarAutocomplete() {
  const inputBusca = document.getElementById('inputBusca');
  const dropdown = document.getElementById('sugestoesDropdown');
  if (!inputBusca || !dropdown) return;

  inputBusca.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    const termo = inputBusca.value.trim();

    if (termo.length < 2) {
      dropdown.classList.add('d-none');
      dropdown.innerHTML = '';
      return;
    }

    debounceTimer = setTimeout(async () => {
      try {
        const resp = await fetch(`backend/buscar.php?nome=${encodeURIComponent(termo)}&sugestoes=1`);
        if (resp.ok) {
          const dados = await resp.json();
          if (dados.sucesso && dados.sugestoes && dados.sugestoes.length > 0) {
            dropdown.innerHTML = dados.sugestoes.map(s => `
              <div class="sugestao-item" onclick="selecionarSugestao('${s.nome}')">
                <img src="${s.sprite_url || ''}" class="sugestao-thumb" onerror="this.style.display='none'">
                <div>
                  <strong class="d-block text-dark">${s.nome}</strong>
                  <span class="small text-muted">#${String(s.id).padStart(3, '0')} · ${s.tipo1}${s.tipo2 ? ' / ' + s.tipo2 : ''}</span>
                </div>
              </div>
            `).join('');
            dropdown.classList.remove('d-none');
            return;
          }
        }
      } catch (e) {
        // Silencioso
      }
      dropdown.classList.add('d-none');
    }, 250);
  });

  // Fecha sugestões ao clicar fora
  document.addEventListener('click', (e) => {
    if (!inputBusca.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.classList.add('d-none');
    }
  });
}

function selecionarSugestao(nome) {
  const inputBusca = document.getElementById('inputBusca');
  const dropdown = document.getElementById('sugestoesDropdown');
  if (inputBusca) inputBusca.value = nome;
  if (dropdown) dropdown.classList.add('d-none');
  buscarPokemon(nome);
}

// =========================================================
// POKÉMON ALEATÓRIO E BOTÕES DE AÇÃO
// =========================================================
const POKEMONS_ALEATORIOS = [
  'Pikachu', 'Charizard', 'Bulbasaur', 'Blastoise', 'Gengar', 'Lucario', 
  'Garchomp', 'Greninja', 'Gardevoir', 'Mimikyu', 'Tyranitar', 'Dragonite',
  'Corviknight', 'Tinkaton', 'Mewtwo', 'Eevee', 'Rayquaza', 'Scizor'
];

function buscarAleatorio() {
  const aleatorio = POKEMONS_ALEATORIOS[Math.floor(Math.random() * POKEMONS_ALEATORIOS.length)];
  const inputBusca = document.getElementById('inputBusca');
  if (inputBusca) inputBusca.value = aleatorio;
  buscarPokemon(aleatorio);
}

function limparTudo() {
  const inputBusca = document.getElementById('inputBusca');
  const select1 = document.getElementById('selectTipo1');
  const select2 = document.getElementById('selectTipo2');
  const cardPokemon = document.getElementById('cardPokemonInfo');
  const resultadoCalculos = document.getElementById('resultadoCalculos');

  if (inputBusca) inputBusca.value = '';
  if (select1) select1.value = 'Fogo';
  if (select2) select2.value = '';
  if (cardPokemon) cardPokemon.classList.add('d-none');
  
  // Recalcula o padrão Fogo
  calcularTipagens('Fogo', null);
  showToast('Calculadora reiniciada.', 'info');
}

// =========================================================
// INICIALIZAÇÃO NO CARREGAMENTO DA PÁGINA
// =========================================================
document.addEventListener('DOMContentLoaded', () => {
  inicializarModal();
  inicializarToast();
  configurarAutocomplete();

  // Formulário de busca
  const formBusca = document.getElementById('formBuscaPokemon');
  if (formBusca) {
    formBusca.addEventListener('submit', (e) => {
      e.preventDefault();
      const termo = document.getElementById('inputBusca').value;
      buscarPokemon(termo);
    });
  }

  // Formulário manual de tipos
  const formTipos = document.getElementById('formCalculadoraTipos');
  if (formTipos) {
    formTipos.addEventListener('submit', (e) => {
      e.preventDefault();
      const t1 = document.getElementById('selectTipo1').value;
      const t2 = document.getElementById('selectTipo2').value;
      calcularTipagens(t1, t2);
    });
  }

  // Alteração imediata dos selects
  const select1 = document.getElementById('selectTipo1');
  const select2 = document.getElementById('selectTipo2');
  if (select1 && select2) {
    const recalcular = () => {
      if (select1.value) {
        calcularTipagens(select1.value, select2.value);
      }
    };
    select1.addEventListener('change', recalcular);
    select2.addEventListener('change', recalcular);
  }

  // Botões de ação rápida
  const btnAleatorio = document.getElementById('btnAleatorio');
  if (btnAleatorio) btnAleatorio.addEventListener('click', buscarAleatorio);

  const btnLimpar = document.getElementById('btnLimpar');
  if (btnLimpar) btnLimpar.addEventListener('click', limparTudo);

  // Inicia com um cálculo inicial (ex: Charizard Fogo/Voador ou Pikachu)
  buscarPokemon('Charizard');
});
