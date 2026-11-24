  <!-- Rodapé Institucional -->
  <footer class="mt-auto py-4 bg-white border-top">
    <div class="container text-center">
      <p class="mb-1 text-muted small">
        <strong>Calculadora de Tipagens Pokémon</strong> &bull; Projeto 
        <a href="https://github.com/lucianoms3467-bit/Pok-friendos.git" target="_blank" class="text-decoration-none fw-semibold text-danger">
          <i class="bi bi-github"></i> Pok-friendos
        </a>
      </p>
      <p class="mb-0 text-secondary" style="font-size: 0.75rem;">
        Desenvolvido com PHP estruturado, MySQL (PDO), Bootstrap 5 (Mobile-First) e PokeAPI.
      </p>
    </div>
  </footer>

  <!-- Inclusão dos Modais Reutilizáveis (Substitutos de Alerts/Confirms nativos) -->
  <?php include __DIR__ . '/modal.php'; ?>

  <!-- Inclusão do Contêiner de Toasts do Bootstrap -->
  <?php include __DIR__ . '/toast.php'; ?>

  <!-- Bootstrap 5 Bundle JS (Popper incluído) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  <!-- Lógica Principal da Aplicação -->
  <script src="assets/js/app.js"></script>
</body>
</html>
