<?php
include 'api/api.php';
session_start();

// Verifica login simples (usuário fixo)
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}
// Lê os dados
$financas = lerJSON("financas.json");
$agendamentos = lerJSON("agendamentos.json");

$total = 0;
foreach($financas as $f){
    if($f['tipo'] === 'receita'){
        $total += $f['valor'];
    }
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
<style>
/* ===== DASHBOARD SECTION ===== */
.dashboard-section {
    max-width: 850px;
    margin: 30px auto;
    font-family: 'Segoe UI', Roboto, Arial, sans-serif;
}

.dashboard-section h2 {
    font-size: 26px;
    color: #222;
    margin-bottom: 20px;
    border-left: 5px solid #c69c6d;
    padding-left: 10px;
}

/* ===== LISTA DE AGENDAMENTOS ===== */
.next-appointments {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* ===== ITEM DE AGENDAMENTO ===== */
.next-appointments li {
    background: linear-gradient(145deg, #ffffff, #f3f7ff);
    padding: 18px 22px;
    border-radius: 14px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.07);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    transition: transform 0.25s, box-shadow 0.25s;
}

.next-appointments li:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

/* ===== INFORMAÇÕES ===== */
.next-appointments li span {
    font-size: 15px;
    color: #333;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.next-appointments li span strong {
    background: #c69c6d;
    color: #fff;
    padding: 4px 8px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
}

/* ===== BOTÃO WHATSAPP ===== */
.btn-whatsapp {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #25D366;
    color: #fff;
    font-weight: 600;
    text-decoration: none;
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(0,0,0,0.12);
}

.btn-whatsapp img {
    width: 22px;
    height: 22px;
}

.btn-whatsapp:hover {
    background: #1ebe57;
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
}

.btn-whatsapp:active {
    transform: scale(0.97);
}

/* ===== RESPONSIVIDADE ===== */
@media (max-width: 480px) {
    .next-appointments li {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    .btn-whatsapp {
        width: 100%;
        justify-content: center;
    }
}
/* ===== CARD DO VALOR TOTAL ===== */
.total-card {
    max-width: 400px;
    margin: 20px auto 0;
    padding: 20px 25px;
    background: linear-gradient(145deg, #ffffff, #f3f7ff);
    border-radius: 14px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.07);
    text-align: center;
    font-size: 18px;
    font-weight: 600;
    color: #c69c6d;
    transition: transform 0.25s, box-shadow 0.25s;
}

.total-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

.total-card strong {
    display: block;
    font-size: 22px;
    margin-top: 8px;
    color: #222;
}

</style>
<body>
<?php include 'includes/header.php'; ?>

<main class="main-content">
  <h1>📊 Dashboard</h1>
  <div class="cards">
    <a style=" text-decoration: none;" href="agendamentos.php"><div class="card">
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
        <?php 
        $valorTotal = 0; // total de todos os agendamentos pagos
        foreach($agendamentosValidos as $a): 
            $cliente = array_filter($clientesValidos, fn($c) => $c['id'] == $a['clienteId']);
            $cliente = reset($cliente);
            $telefone = $cliente['telefone'] ?? null;
            $nomeCliente = $cliente['nome'] ?? 'Sem nome';
            $hora = isset($a['dataHora']) ? date('H:i', strtotime($a['dataHora'])) : 'N/A';
            $valorTotal += $a['pago'] ? ($a['preco'] ?? 0) : 0; // soma somente pagos
        ?>
            <li class="<?= !empty($a['pago']) && $a['pago'] ? 'agendamento-pago' : '' ?>">
                <span>
                    <strong><?= $hora ?></strong> - <?= $nomeCliente ?> - <?= $a['servico'] ?>
                    <?php if(!empty($a['pago']) && $a['pago']): ?>
                        <span class="badge-pago">✔ Pago</span>
                    <?php endif; ?>
                </span>

                <?php if($telefone): 
                    $mensagem = "Prezado(a) $nomeCliente,\n\nSeu atendimento está agendado para às $hora.\n\nCaso haja necessidade de reagendamento, solicitamos que nos avise com antecedência.\n\nAgradecemos a sua confiança em nossos serviços.\n\n_Mensagem enviada automaticamente._";
                    $mensagem = urlencode($mensagem);
                ?>
                    <a href="https://wa.me/55<?= preg_replace('/\D/', '', $telefone) ?>?text=<?= $mensagem ?>" 
                       target="_blank" class="btn-whatsapp">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp"> WhatsApp
                    </a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- CARD DO VALOR TOTAL -->
    <div class="total-card">
        <span>Total Recebido:</span>
        <strong>R$ <?= number_format($valorTotal, 2, ',', '.') ?></strong>
    </div>
</div>

</main>


</body>
</html>
