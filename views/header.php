<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calculadora de Tipagens Pokémon | Pok-friendos</title>
  
  <!-- Bootstrap 5 CSS (Mobile-First) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <!-- Fonte Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Estilos Customizados -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- Barra de Navegação Superior (Estilo Pokédex) -->
  <nav class="navbar navbar-dark navbar-pokedex py-2 sticky-top shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
      <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-white fs-4 m-0" href="index.php">
        <svg class="brand-icon" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="50" cy="50" r="48" fill="#FFFFFF" stroke="#222" stroke-width="4"/>
          <path d="M2 50 A48 48 0 0 1 98 50 Z" fill="#EE1515"/>
          <line x1="2" y1="50" x2="98" y2="50" stroke="#222" stroke-width="6"/>
          <circle cx="50" cy="50" r="16" fill="#FFFFFF" stroke="#222" stroke-width="6"/>
          <circle cx="50" cy="50" r="8" fill="#FFFFFF" stroke="#999" stroke-width="2"/>
        </svg>
        <span>Pok-friendos <span class="badge bg-warning text-dark fs-6 rounded-pill align-middle">Tipagens</span></span>
      </a>

      <div class="d-flex align-items-center gap-2">
        <!-- Indicador de Status do Banco MySQL -->
        <?php if (isset($pdo) && $pdo !== null): ?>
          <span class="badge bg-success-subtle text-success border border-success-subtle d-none d-sm-inline-flex align-items-center gap-1 rounded-pill px-2 py-1 small" title="MySQL Conectado via PDO">
            <i class="bi bi-database-check"></i> MySQL Online
          </span>
        <?php else: ?>
          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle d-none d-sm-inline-flex align-items-center gap-1 rounded-pill px-2 py-1 small" title="Conexão local ativa">
            <i class="bi bi-database-exclamation"></i> Motor Nativo
          </span>
        <?php endif; ?>

        <!-- Botão de Ajuda / Como Funciona (Abre Modal) -->
        <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#infoAjudaModal">
          <i class="bi bi-question-circle-fill"></i>
          <span class="d-none d-sm-inline">Como Funciona</span>
        </button>
      </div>
    </div>
  </nav>
