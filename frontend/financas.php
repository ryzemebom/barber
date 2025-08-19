<?php
include 'functions.php';

// Lê os dados
$financas = lerJSON("financas.json");
$agendamentos = lerJSON("agendamentos.json");

// Excluir registro financeiro se existir query ?delete_id
if(isset($_GET['delete_id'])){
    $idDelete = $_GET['delete_id'];
    foreach($financas as $k => $f){
        if($f['id'] == $idDelete){
            unset($financas[$k]);
            break;
        }
    }
    $financas = array_values($financas); // reindexa
    salvarJSON("financas.json", $financas);
    header("Location: financas.php");
    exit;
}

// Total recebido
$total = 0;
foreach($financas as $f){
    if($f['tipo'] === 'receita'){
        $total += $f['valor'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Finanças - Barbearia</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .receita { color: green; font-weight: bold; }
        .despesa { color: red; font-weight: bold; }
        .dashboard { display: flex; gap: 20px; margin-bottom: 20px; }
        .card { padding: 15px; border-radius: 8px; background: #f2f2f2; flex: 1; text-align: center; }
        .btn-delete { background: #c0392b; color: #fff; padding: 5px 10px; border-radius: 5px; text-decoration: none; }
        .btn-delete:hover { background: #e74c3c; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="main-content">
    <h1>Finanças</h1>

    <div class="dashboard">
        <div class="card">
            <h3>Total Recebido</h3>
            <p class="receita">R$ <?= number_format($total,2,",",".") ?></p>
        </div>
        <div class="card">
            <h3>Agendamentos Pagos</h3>
            <p class="receita"><?= count(array_filter($agendamentos, fn($a) => $a['pago'])) ?></p>
        </div>
        <div class="card">
            <h3>Agendamentos Pendentes</h3>
            <p class="despesa"><?= count(array_filter($agendamentos, fn($a) => !$a['pago'])) ?></p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Valor (R$)</th>
                <th>Status de pagamento</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($financas)): ?>
                <?php foreach($financas as $f): ?>
                    <tr>
                        <td><?= $f['data'] ?></td>
                        <td><?= $f['descricao'] ?></td>
                        <td class="receita"><?= number_format($f['valor'],2,",",".") ?></td>
                        <td><?= $f['tipo'] === 'receita' ? 'Pago' : 'Outro' ?></td>
                        <td>
                            <a href="financas.php?delete_id=<?= $f['id'] ?>" class="btn-delete" onclick="return confirm('Deseja realmente excluir este registro?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">Nenhum registro financeiro.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
