<?php
include 'api/api.php';
session_start();

// Verifica login simples (usuário fixo)
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

// Função para ler JSON
function lerJSON($arquivo) {
    $caminho = __DIR__ . "/data/$arquivo";
    if(!file_exists($caminho)) {
        file_put_contents($caminho, json_encode([]));
    }
    $json = file_get_contents($caminho);
    return json_decode($json, true);
}

// Carrega dados
$clientes = lerJSON("clientes.json");
$servicos = lerJSON("servico.json");
$agendamentos = lerJSON("agendamentos.json");

// Garante que sejam arrays
$clientes = is_array($clientes) ? $clientes : [];
$servicos = is_array($servicos) ? $servicos : [];
$agendamentos = is_array($agendamentos) ? $agendamentos : [];

// Filtra apenas registros válidos
$clientesValidos = array_filter($clientes, fn($c) => isset($c['id']) && !empty($c['id']));
$servicosValidos = array_filter($servicos, fn($s) => isset($s['id']) && !empty($s['id']));
$agendamentosValidos = array_filter($agendamentos, fn($a) => isset($a['id']) && !empty($a['id']));

// Estatísticas
$totalClientes = count($clientesValidos);
$totalServicos = count($servicosValidos);
$totalAgendamentos = count($agendamentosValidos);

// Serviço mais popular
$servicoCount = [];
foreach($agendamentosValidos as $a){
    $idServico = $a['servicoId'] ?? null;
    if($idServico) $servicoCount[$idServico] = ($servicoCount[$idServico] ?? 0) + 1;
}
arsort($servicoCount);
$servicoMaisPopularId = array_key_first($servicoCount);
$quantidadeMaisPopular = $servicoCount[$servicoMaisPopularId] ?? 0;
$nomeServicoMaisPopular = 'N/A';
if($servicoMaisPopularId){
    $s = array_filter($servicosValidos, fn($s) => $s['id'] == $servicoMaisPopularId);
    $s = reset($s);
    $nomeServicoMaisPopular = $s['nome'] ?? 'N/A';
}

?>
<style>
.cards {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}
.card {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    flex: 1;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.next-appointments {
    list-style: none;
    padding-left: 0;
}

.next-appointments li {
    background: #fff;
    padding: 12px 15px;
    margin-bottom: 10px;
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-whatsapp {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #25D366;
    color: white;
    padding: 5px 10px;
    border-radius: 6px;
    text-decoration: none;
    transition: 0.3s;
}

.btn-whatsapp img {
    height: 16px;
    margin-right: 5px;
}

.btn-whatsapp:hover {
    background: #1ebe5a;
}
</style>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Barbearia</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<main class="main-content">
  <h1>📊 Dashboard</h1>
  <div class="cards">
    <a style=" text-decoration: none;" href="financas.php"><div class="card">
      <h3>Total de Agendamentos</h3>
      <p style=" text-decoration: none;"><?= $totalAgendamentos ?></p>
    </div></a>
    <a style=" text-decoration: none;" href="clientes.php"> <div class="card">
      <h3>Total de Clientes</h3>
      <p><?= $totalClientes ?></p>
    </div> </a>
 <a style=" text-decoration: none;" href="servicos.php">    <div class="card">
      <h3>Total de Serviços</h3>
      <p><?= $totalServicos ?></p>
    </div></a>
  </div>

  <div class="dashboard-section">
    <h2>Todos os Agendamentos</h2>
    <ul class="next-appointments">
        <?php foreach($agendamentosValidos as $a): ?>
            <?php 
                $cliente = array_filter($clientesValidos, fn($c) => $c['id'] == $a['clienteId']);
                $cliente = reset($cliente);
                $telefone = $cliente['telefone'] ?? null;
                $nomeCliente = $cliente['nome'] ?? 'Sem nome';
                $hora = isset($a['dataHora']) ? date('H:i', strtotime($a['dataHora'])) : 'N/A';
            ?>
            <li>
                <span><strong><?= $hora ?>:</strong> <?= $nomeCliente ?> - <?= $a['servico'] ?></span>
                <?php if($telefone): ?>
                    <a href="https://wa.me/55<?= $telefone ?>" target="_blank" class="btn-whatsapp">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp"> WhatsApp
                    </a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
  </div>

  <canvas id="agendaChart" width="400" height="200"></canvas>
</main>


</body>
</html>
