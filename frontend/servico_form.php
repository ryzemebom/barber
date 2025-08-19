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

$id = $_GET['id'] ?? null;
$servicos = lerJSON("servico.json");
$servico = null;

// Se for edição, buscar serviço existente
if ($id) {
    foreach($servicos as $s) {
        if($s['id'] == $id){
            $servico = $s;
            break;
        }
    }
}

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $novoServico = [
        "id" => isset($_POST['id']) ? $_POST['id'] : (count($servicos) > 0 ? end($servicos)['id'] + 1 : 1),
        "nome" => $_POST['nome'],
        "duracao" => intval($_POST['duracao']),
        "preco" => floatval($_POST['preco'])
    ];

    if(isset($_POST['id'])) {
        // Atualizar serviço
        foreach($servicos as $k => $s){
            if($s['id'] == $_POST['id']){
                $servicos[$k] = $novoServico;
                break;
            }
        }
    } else {
        // Adicionar novo serviço
        $servicos[] = $novoServico;
    }

    salvarJSON("servico.json", $servicos);
    header("Location: servicos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id ? "Editar Serviço" : "Adicionar Serviço"; ?> - Barbearia</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/header.php"; ?>

<div class="main-content">
    <h1><?= $id ? "Editar Serviço" : "Adicionar Serviço"; ?></h1>
    <form method="POST">
        <?php if($id): ?>
            <input type="hidden" name="id" value="<?= $id ?>">
        <?php endif; ?>

        <label for="nome">Nome do Serviço:</label>
        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($servico['nome'] ?? '') ?>" required>

        <label for="duracao">Duração (minutos):</label>
        <input type="number" name="duracao" id="duracao" value="<?= htmlspecialchars($servico['duracao'] ?? '') ?>" required>

        <label for="preco">Preço (R$):</label>
        <input type="number" step="0.01" name="preco" id="preco" value="<?= htmlspecialchars($servico['preco'] ?? '') ?>" required>

        <button type="submit"><?= $id ? "Salvar Alterações" : "Adicionar Serviço"; ?></button>
        <a href="servicos.php" class="btn" style="background:#203a43;">Cancelar</a>
    </form>
</div>
</body>
</html>
