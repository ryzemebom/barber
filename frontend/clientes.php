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

// Lista clientes
$clientes = lerJSON("clientes.json");

// Garante que seja sempre array
if(!is_array($clientes)){
    $clientes = [];
}

// Deletar cliente
if(isset($_GET['delete'])){
    $idDelete = $_GET['delete'];
    foreach($clientes as $k => $c){
        if($c['id'] == $idDelete){
            unset($clientes[$k]);
            break;
        }
    }
    $clientes = array_values($clientes); // reindexa array
    salvarJSON("clientes.json", $clientes);
    header("Location: cliente.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Clientes - Barbearia</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .btn-whatsapp {
            display: inline-flex;
            align-items: center;
            background: #25D366;
            padding: 5px 10px;
            border-radius: 6px;
            color: white;
            text-decoration: none;
            margin-left: 5px;
            transition: 0.3s;
        }
        .btn-whatsapp img {
            width: 16px;
            height: 16px;
            margin-right: 5px;
        }
        .btn-whatsapp:hover {
            background: #1ebe5a;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="main-content">
    <h1>Clientes</h1>
    <a href="cliente_form.php" class="btn">Adicionar Cliente</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if(count($clientes) > 0): ?>
            <?php foreach($clientes as $c): ?>
                <?php 
                    $telefone = $c['telefone'];
                    $mensagem = urlencode("Olá " . $c['nome'] . ", seu horário na barbearia está confirmado!");
                    $whatsappLink = "https://wa.me/55$telefone?text=$mensagem";
                ?>
                <tr>
                    <td data-label="ID"><?= $c['id'] ?? 'N/A' ?></td>
                    <td data-label="Nome"><?= $c['nome'] ?? 'N/A' ?></td>
                    <td data-label="Telefone"><?= $c['telefone'] ?? 'N/A' ?></td>
                    <td data-label="Ações">
                        <a href="cliente_form.php?id=<?= $c['id'] ?>" class="btn-edit">Editar</a>
                        <a href="cliente_delete.php?id=<?= $c['id'] ?>" class="btn-delete" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">Excluir</a>
                        <?php if(!empty($c['telefone'])): ?>
                        <a href="<?= $whatsappLink ?>" target="_blank" class="btn-whatsapp">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp"> WhatsApp
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align:center;">Nenhum cliente encontrado</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
