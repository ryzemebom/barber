<?php
session_start();

$error = "";

// Usuário e senha pré-definidos
$usuarioValido = "arthur";
$senhaValida = "1234";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST["usuario"];
    $senha = $_POST["senha"];

    if ($usuario === $usuarioValido && $senha === $senhaValida) {
        $_SESSION["usuario"] = $usuario;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Usuário ou senha inválidos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <title>Login - Sistema Barbearia</title>
</head>
<style>
/* Tela de carregamento */
#loading-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    z-index: 9999;
    transition: opacity 0.5s ease, visibility 0.5s ease;
}

#loading-screen.hidden {
    opacity: 0;
    visibility: hidden;
}

/* Loader animado */
.loader {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #3498db;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
    margin-bottom: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}


</style>
<body class="login-page">
<div id="loading-screen">
    <div class="loader"></div>
    <p>Carregando...</p>
</div>

    <div class="login-container">
        <h1>Barbearia Login</h1>
        <?php if ($error): ?>
            <p style="color:red;"><?= $error ?></p>
        <?php endif; ?>
        <form class="login-form" method="POST">
            <input type="text" name="usuario" placeholder="Usuário" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
<script>
    window.addEventListener("load", function() {
        // deixa visível por 500ms para dar tempo de ver
        setTimeout(() => {
            document.getElementById("loading-screen").classList.add("hidden");
        }, 300);
    });
</script>


</body>
</html>
