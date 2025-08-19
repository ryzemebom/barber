<form method="POST" action="">
    <label>Valor da Despesa:</label>
    <input type="number" step="0.01" name="valor" required>
    <button type="submit">Salvar</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valor = $_POST['valor'];
    $saida = shell_exec("java -cp ../backend Financas adicionarDespesa $valor");
    echo "<p>Resposta: $saida</p>";
}
?>
