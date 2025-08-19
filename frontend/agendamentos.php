<?php
include 'functions.php';

// Lê os dados
$agendamentos = lerJSON("agendamentos.json");
$financas = lerJSON("financas.json");

// Deletar agendamento
if(isset($_GET['delete'])){
    $idDelete = $_GET['delete'];
    foreach($agendamentos as $k => $a){
        if($a['id'] == $idDelete){
            // Remover do agendamentos
            unset($agendamentos[$k]);

            // Remover do financeiro caso esteja marcado como pago
            foreach($financas as $fk => $f){
                if($f['descricao'] === "Agendamento de {$a['clienteNome']} - {$a['servico']}" && $f['valor'] == $a['preco']){
                    unset($financas[$fk]);
                    break;
                }
            }

            break;
        }
    }
    // Reindexar arrays
    $agendamentos = array_values($agendamentos);
    $financas = array_values($financas);

    // Salvar arquivos
    salvarJSON("agendamentos.json", $agendamentos);
    salvarJSON("financas.json", $financas);

    header("Location: agendamentos.php");
    exit;
}

// Alternar pagamento (marcar/desmarcar)
if(isset($_GET['pago_id'])){
    $idPago = $_GET['pago_id'];
    foreach($agendamentos as $k => $a){
        if($a['id'] == $idPago){
            if(!isset($a['pago']) || !$a['pago']){
                // Marcar como pago
                $agendamentos[$k]['pago'] = true;
                $financas[] = [
                    "data" => date("Y-m-d"),
                    "descricao" => "Agendamento de {$a['clienteNome']} - {$a['servico']}",
                    "valor" => $a['preco'],
                    "tipo" => "receita"
                ];
            } else {
                // Desmarcar pagamento
                $agendamentos[$k]['pago'] = false;
                // Remover o registro financeiro correspondente
                foreach($financas as $fk => $f){
                    if($f['descricao'] === "Agendamento de {$a['clienteNome']} - {$a['servico']}" && $f['valor'] == $a['preco']){
                        unset($financas[$fk]);
                        break;
                    }
                }
                $financas = array_values($financas);
            }

            salvarJSON("agendamentos.json", $agendamentos);
            salvarJSON("financas.json", $financas);
            break;
        }
    }
    header("Location: agendamentos.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Agendamentos - Barbearia</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 0.95rem;
        }

        .table thead {
            background-color: #203a43;
            color: #fff;
        }

        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            font-weight: 600;
        }

        .table tr:nth-child(even) {
            background-color: #f7f9fa;
        }

        .table tr:hover {
            background-color: #e2f0f7;
        }

        .table thead th:first-child {
            border-top-left-radius: 8px;
        }
        .table thead th:last-child {
            border-top-right-radius: 8px;
        }

        .table td[data-label="Ações"] {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .btn-pago {
            display: inline-block;
            width: 170px;
            text-align:center;
            padding: 6px 12px;
            background-color: #28a745; /* Verde */
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: background-color 0.3s;
            margin-left: 5px;
        }

        .btn-pago:hover {
            background-color: #218838; /* Verde mais escuro */
        }

        .btn-pago.desmarcado {
            background-color: #dc3545; /* Vermelho */
            width: 170px;
        }

        .btn-pago.desmarcado:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="main-content">
    <h1>Agendamentos</h1>
    <a href="agendamento_form.php" class="btn">Novo Agendamento</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Serviço</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Preço</th>
                <th>Pago?</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if(count($agendamentos) > 0): ?>
            <?php foreach($agendamentos as $a): 
                $dataHora = $a['dataHora'] ?? null;
                if($dataHora){
                    [$data, $hora] = explode("T", $dataHora);
                } else {
                    $data = $hora = 'N/A';
                }
            ?>
                <tr>
                    <td data-label="ID"><?= $a['id'] ?? 'N/A' ?></td>
                    <td data-label="Cliente"><?= $a['clienteNome'] ?? 'N/A' ?></td>
                    <td data-label="Serviço"><?= $a['servico'] ?? 'N/A' ?></td>
                    <td data-label="Data"><?= $data ?></td>
                    <td data-label="Hora"><?= $hora ?></td>
                    <td data-label="Preço">R$ <?= number_format($a['preco'] ?? 0, 2, ",", ".") ?></td>
                    <td data-label="Pago?"><?= isset($a['pago']) && $a['pago'] ? "Sim" : "Não" ?></td>
                    <td data-label="Ações">
                        <a href="agendamento_form.php?id=<?= $a['id'] ?>" class="btn-edit">Editar</a>
                        <a href="agendamentos.php?delete=<?= $a['id'] ?>" class="btn-delete" onclick="return confirm('Tem certeza que deseja excluir este agendamento?')">Excluir</a>
                        <a href="agendamentos.php?pago_id=<?= $a['id'] ?>" 
                           class="btn-pago <?= isset($a['pago']) && $a['pago'] ? 'desmarcado' : '' ?>" 
                           onclick="return confirm('Deseja alternar o status de pagamento?')">
                            <?= isset($a['pago']) && $a['pago'] ? "Desmarcar pagamento" : "Marcar como pago" ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align:center;">Nenhum agendamento encontrado</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
