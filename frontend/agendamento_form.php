<?php
include 'functions.php';

$clientes = lerJSON("clientes.json");
$servicos = lerJSON("servico.json");
$agendamentos = lerJSON("agendamentos.json");

$id = $_GET['id'] ?? null;
$agendamento = null;

if ($id) {
    foreach($agendamentos as $a){
        if($a['id'] == $id){
            $agendamento = $a;
            break;
        }
    }
}

// Processa envio do formulário
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $clienteId = $_POST['cliente'];
    $servicoId = $_POST['servico'];
    $dataHora = $_POST['data'] . "T" . $_POST['hora'];

    // Busca dados completos do cliente e serviço
    $clienteNome = '';
    foreach($clientes as $c){
        if($c['id'] == $clienteId){
            $clienteNome = $c['nome'];
            break;
        }
    }

    $servicoNome = '';
    $preco = 0;
    foreach($servicos as $s){
        if($s['id'] == $servicoId){
            $servicoNome = $s['nome'];
            $preco = floatval($s['preco']);
            break;
        }
    }

    $novoAgendamento = [
        "id" => isset($_POST['id']) ? $_POST['id'] : (count($agendamentos) > 0 ? end($agendamentos)['id'] + 1 : 1),
        "clienteId" => $clienteId,
        "clienteNome" => $clienteNome,
        "servicoId" => $servicoId,
        "servico" => $servicoNome,
        "preco" => $preco,
        "dataHora" => $dataHora,
        "pago" => $agendamento['pago'] ?? false
    ];

    if (isset($_POST['id'])) {
        foreach($agendamentos as $k => $a){
            if($a['id'] == $_POST['id']){
                $agendamentos[$k] = $novoAgendamento;
                break;
            }
        }
    } else {
        $agendamentos[] = $novoAgendamento;
    }

    salvarJSON("agendamentos.json", $agendamentos);
    header("Location: agendamentos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? "Editar" : "Novo" ?> Agendamento</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="main-content">
    <h1><?= $id ? "Editar" : "Novo" ?> Agendamento</h1>
    <form method="POST">
        <?php if($id): ?>
            <input type="hidden" name="id" value="<?= $id ?>">
        <?php endif; ?>

        <label>Cliente:</label>
        <select name="cliente" required>
            <option value="">Selecione</option>
            <?php foreach($clientes as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($agendamento['clienteId'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                    <?= $c['nome'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Serviço:</label>
        <select name="servico" required>
            <option value="">Selecione</option>
            <?php foreach($servicos as $s): ?>
                <option value="<?= $s['id'] ?>" <?= ($agendamento['servicoId'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                    <?= $s['nome'] ?> - R$ <?= number_format($s['preco'],2,",",".") ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Data:</label>
        <input type="date" name="data" value="<?= $agendamento['dataHora'] ? explode("T", $agendamento['dataHora'])[0] : '' ?>" required>

        <label>Hora:</label>
        <input type="time" name="hora" value="<?= $agendamento['dataHora'] ? explode("T", $agendamento['dataHora'])[1] : '' ?>" required>

        <button type="submit" class="btn"><?= $id ? "Atualizar" : "Agendar" ?></button>
        <a href="agendamentos.php" class="btn" style="background:#203a43;">Cancelar</a>
    </form>
</div>
</body>
</html>
