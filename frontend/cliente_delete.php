<?php
// Função para ler JSON e retornar array
function lerJSON($arquivo) {
    if(!file_exists($arquivo)) return [];
    $conteudo = file_get_contents($arquivo);
    $dados = json_decode($conteudo, true);
    return is_array($dados) ? $dados : [];
}

// Função para salvar array no JSON
function salvarJSON($arquivo, $array) {
    file_put_contents($arquivo, json_encode($array, JSON_PRETTY_PRINT));
}

if(isset($_GET['id'])){
    $id = $_GET['id'];

    // Lê os clientes do JSON
    $clientes = lerJSON("data/clientes.json");

    // Remove o cliente com o ID fornecido
    $clientes = array_filter($clientes, fn($c) => $c['id'] != $id);
    $clientes = array_values($clientes); // reindexa

    // Salva de volta
    salvarJSON("data/clientes.json", $clientes);

    // Redireciona
    header("Location: clientes.php");
    exit;
} else {
    echo "ID do cliente não fornecido!";
}
?>
