<?php
include 'api/api.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $response = callAPI("DELETE", "servicos/$id");
    header("Location: servicos.php");
    exit;
} else {
    echo "ID do serviço não fornecido!";
}
