<?php
if(isset($_GET['id'])) {
    $id = $_GET['id'];

    $url = "http://localhost:8080/api/whatsapp/notificar/$id";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    echo "<script>alert('Resposta: $response'); window.location='clientes.php';</script>";
} else {
    echo "<script>alert('ID do cliente não fornecido'); window.location='clientes.php';</script>";
}
?>
