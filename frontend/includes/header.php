<aside class="sidebar">
  <h2>💈 Barbearia</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="clientes.php">Clientes</a>
    <a href="agendamentos.php">Agendamentos</a>
    <a href="servicos.php">Serviços</a>
    <a href="financas.php">Financias</a>
    <a href="index.php">Sair</a>
  </nav>
</aside>

<div id="loading-screen">
    <div class="loader"></div>
    <p>Carregando...</p>
</div>

<script>
    window.addEventListener("load", function() {
        // deixa visível por 500ms para dar tempo de ver
        setTimeout(() => {
            document.getElementById("loading-screen").classList.add("hidden");
        }, 350);
    });
</script>

<style>
  /* Tela de carregamento */
/* Tela de carregamento só no conteúdo */
#loading-screen {
    position: fixed;
    top: 0;
    left: 226px; /* largura da sidebar */
    width: calc(100% - 250px); /* ocupa só o espaço fora do menu */
    height: 100%;
    background: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    z-index: 9999;
    transition: opacity 0.5s ease, visibility 0.5s ease;
}

#loading-screen.hidden {
    opacity: 0;
    visibility: hidden;
}

/* Loader animado */
.loader {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #3498db;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
    margin-bottom: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
