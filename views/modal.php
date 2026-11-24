<!-- Modal Global do Sistema (Substituto de window.alert / confirm nativos) -->
<div class="modal fade" id="appModal" tabindex="-1" aria-labelledby="appModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow border-0 rounded-4">
      <div class="modal-header border-0 pb-0" id="appModalHeader">
        <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="appModalLabel">
          <i class="bi bi-info-circle-fill text-primary" id="appModalIcon"></i>
          <span id="appModalTitle">Aviso</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body py-3" id="appModalBody">
        <p class="mb-0 text-secondary" id="appModalMessage">Mensagem informativa.</p>
      </div>
      <div class="modal-footer border-0 pt-0" id="appModalFooter">
        <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal" id="appModalCancelBtn" style="display: none;">Cancelar</button>
        <button type="button" class="btn btn-primary px-4 rounded-pill" data-bs-dismiss="modal" id="appModalConfirmBtn">Entendi</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Informativo: Como Funciona o Cálculo de Tipos -->
<div class="modal fade" id="infoAjudaModal" tabindex="-1" aria-labelledby="infoAjudaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow border-0 rounded-4">
      <div class="modal-header bg-danger text-white rounded-top-4">
        <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="infoAjudaModalLabel">
          <i class="bi bi-calculator-fill"></i> Como Funciona o Cálculo de Tipagem
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body p-4">
        <h6 class="fw-bold text-dark mb-2">Multiplicadores Defensivos:</h6>
        <p class="small text-muted mb-3">Quando o Pokémon sofre um ataque, os multiplicadores de seus dois tipos são multiplicados entre si:</p>
        <div class="row g-2 mb-4">
          <div class="col-12 col-sm-6">
            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2">
              <span class="badge bg-danger fs-6">4x</span>
              <span class="small"><strong>Super Fraqueza:</strong> Ambos os tipos sofrem 2x de dano (2 × 2 = 4x).</span>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2">
              <span class="badge bg-warning text-dark fs-6">2x</span>
              <span class="small"><strong>Fraqueza:</strong> Dano dobrado contra a combinação defensiva.</span>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2">
              <span class="badge bg-success fs-6">0.5x</span>
              <span class="small"><strong>Resistência:</strong> Dano reduzido pela metade contra o golpe.</span>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2">
              <span class="badge bg-info text-dark fs-6">0.25x</span>
              <span class="small"><strong>Super Resistência:</strong> Ambos os tipos resistem (0.5 × 0.5 = 0.25x).</span>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2">
              <span class="badge bg-dark fs-6">0x</span>
              <span class="small"><strong>Imunidade:</strong> O Pokémon não sofre dano desse tipo de ataque.</span>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2">
              <span class="badge bg-secondary fs-6">1x</span>
              <span class="small"><strong>Dano Normal:</strong> Dano neutro padrão sem modificador.</span>
            </div>
          </div>
        </div>

        <h6 class="fw-bold text-dark mb-2">Super Efetivo no Ataque (STAB):</h6>
        <p class="small text-muted mb-0">Indica contra quais tipagens adversárias este Pokémon causará dano super efetivo (2x) utilizando seus ataques do mesmo tipo (STAB).</p>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-danger px-4 rounded-pill" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
