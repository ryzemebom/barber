<?php
// Funções para ler e salvar JSON
function lerJSON($arquivo) {
    $caminho = __DIR__ . "/data/$arquivo";
    if(!file_exists($caminho)) {
        file_put_contents($caminho, json_encode([]));
    }
    $json = file_get_contents($caminho);
    return json_decode($json, true);
}

function salvarJSON($arquivo, $dados) {
    $caminho = __DIR__ . "/data/$arquivo";
    file_put_contents($caminho, json_encode($dados, JSON_PRETTY_PRINT));
}

// Lista serviços
$servicos = lerJSON("servico.json");

// Deletar serviço se existir parâmetro delete_id
if (isset($_GET['delete_id'])) {
    $idDelete = $_GET['delete_id'];
    foreach($servicos as $k => $s){
        if($s['id'] == $idDelete){
            unset($servicos[$k]);
            break;
        }
    }
    $servicos = array_values($servicos); // reindexa array
    salvarJSON("servico.json", $servicos);
    header("Location: servicos.php");
    exit;
}

// Garante que seja array
if(!is_array($servicos)){
    $servicos = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços - Barbearia</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/header.php"; ?>

<div class="main-content">
    <h1>Serviços</h1>
    <a href="servico_form.php" class="btn">Adicionar Serviço</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Duração (min)</th>
                <th>Preço (R$)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($servicos)): ?>
                <?php foreach($servicos as $s): ?>
                    <tr>
                        <td><?= $s['id'] ?? 'N/A' ?></td>
                        <td><?= $s['nome'] ?? 'N/A' ?></td>
                        <td><?= $s['duracao'] ?? 'N/A' ?></td>
                        <td><?= $s['preco'] ?? 'N/A' ?></td>
                        <td>
                            <a href="servico_form.php?id=<?= $s['id'] ?>" class="btn-edit">Editar</a>
                            <a href="servicos.php?delete_id=<?= $s['id'] ?>" class="btn-delete" onclick="return confirm('Deseja realmente excluir este serviço?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">Nenhum serviço cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
