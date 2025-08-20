<?php
include 'functions.php';

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
        table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}

thead {
    background: #007bff;
    color: #fff;
}

thead th {
    padding: 14px 12px;
    text-align: left;
    font-size: 15px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

tbody td {
    padding: 12px;
    font-size: 14px;
    color: #333;
    border-bottom: 1px solid #eee;
}

/* Listrado */
tbody tr:nth-child(even) {
    background: #f9f9f9;
}

/* Hover */
tbody tr:hover {
    background: #f1f7ff;
    transition: 0.2s;
}
        .btn-edit {
            display: inline-flex;
            align-items: center;
            background: #1000a1ff;
            padding: 8px 10px;
            border-radius: 6px;
            color: white;
            text-decoration: none;
            margin-left: 5px;
            transition: 0.3s;
        }
        .btn-edit:hover {
            background: #0069d9;
        }

        .btn-delete {
            display: inline-flex;
            align-items: center;
            background: #970a18ff;
            padding: 8px 10px;
            border-radius: 6px;
            color: white;
            text-decoration: none;
            margin-left: 5px;
            transition: 0.3s;
        }
        .btn-delete:hover {
            background: #c82333;
        }

        .btn-whatsapp {
            display: inline-flex;
            align-items: center;
            background: #0b910dff;
            padding: 8px 10px;
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
                    $mensagem = urlencode("Olá " . $c['nome'] . ", seu horário na barbearia está confirmado! \n\n_Mensagem enviada automaticamente._");
                    $whatsappLink = "https://wa.me/55$telefone?text=$mensagem";
                ?>
                <tr>
                    <td data-label="ID"><?= $c['id'] ?? 'N/A' ?></td>
                    <td data-label="Nome"><?= $c['nome'] ?? 'N/A' ?></td>
                    <td data-label="Telefone"><?= $c['telefone'] ?? 'N/A' ?></td>
                    <td data-label="Ações">
                        <a href="cliente_form.php?id=<?= $c['id'] ?>" class="btn-edit">
                            ✏️ Editar
                        </a>
                        <a href="cliente.php?delete=<?= $c['id'] ?>" class="btn-delete" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
                            🗑️ Excluir
                        </a>
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
