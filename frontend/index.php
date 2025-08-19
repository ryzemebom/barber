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

<body class="login-page">
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
</body>
</html>
