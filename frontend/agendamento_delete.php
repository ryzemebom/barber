<?php
include 'api/api.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $response = callAPI("DELETE", "agendamentos/$id");
    header("Location: agendamentos.php");
    exit;
} else {
    echo "ID do agendamento não fornecido!";
}
