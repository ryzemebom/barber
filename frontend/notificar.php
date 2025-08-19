<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteId = $_POST['cliente_id'];

    $url = "http://localhost:8080/api/notificar"; 
    $data = ["clienteId" => $clienteId];

    $options = [
        "http" => [
            "header"  => "Content-Type: application/json\r\n",
            "method"  => "POST",
            "content" => json_encode($data)
        ]
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    echo "Resposta do backend: " . $result;
}
?>
<form method="POST">
    <label>Cliente ID:</label>
    <input type="text" name="cliente_id" required>
    <button type="submit">Enviar WhatsApp</button>
</form>
