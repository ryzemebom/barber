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
$clientes = lerJSON("clientes.json");
$cliente = null;

// Se for edição, buscar os dados do cliente
if ($id) {
    foreach($clientes as $c) {
        if($c['id'] == $id){
            $cliente = $c;
            break;
        }
    }
}

// Processar envio do formulário
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $novoCliente = [
        "id" => isset($_POST['id']) ? $_POST['id'] : (count($clientes) > 0 ? end($clientes)['id'] + 1 : 1),
        "nome" => $_POST["nome"],
        "telefone" => $_POST["telefone"],
        "email" => $_POST["email"] ?? ""
    ];

    if(isset($_POST['id'])) {
        // Atualizar cliente
        foreach($clientes as $k => $c){
            if($c['id'] == $_POST['id']){
                $clientes[$k] = $novoCliente;
                break;
            }
        }
    } else {
        // Adicionar novo cliente
        $clientes[] = $novoCliente;
    }

    salvarJSON("clientes.json", $clientes);
    header("Location: clientes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <title><?= $id ? "Editar" : "Adicionar" ?> Cliente - Barbearia</title>
</head>
<body>
<?php include 'includes/header.php'; ?>

<main class="main-content">
    <h1><?= $id ? "Editar Cliente" : "Adicionar Cliente" ?></h1>

    <form method="POST">
        <?php if($id): ?>
            <input type="hidden" name="id" value="<?= $id ?>">
        <?php endif; ?>

        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?= $cliente['nome'] ?? '' ?>" required>

<label for="telefone">Telefone:</label>
<input type="text" id="telefone" name="telefone" value="<?= $cliente['telefone'] ?? '' ?>" required
       oninput="formatarTelefone(this)" maxlength="15" placeholder="(99) 99999-9999">

     <!--   <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= $cliente['email'] ?? '' ?>"> -->

        <button type="submit" class="btn"><?= $id ? "Atualizar" : "Cadastrar" ?></button>
        <a href="clientes.php" class="btn" style="background:#203a43;">Cancelar</a>
    </form>
</main>
</body>

<script>
function formatarTelefone(input) {
    // Remove tudo que não for número
    let numero = input.value.replace(/\D/g, '');

    // Limita a 11 caracteres (ex: 11988887777)
    numero = numero.substring(0, 11);

    input.value = numero;
}

</script>
</html>
