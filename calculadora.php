<?php
$resultado = null;
$erro = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $a = $_POST["a"];
    $b = $_POST["b"];
    $operacao = $_POST["operacao"];

    if (is_numeric($a) && is_numeric($b)) {

        switch ($operacao) {
            case "+":
                $resultado = $a + $b;
                break;
            case "-":
                $resultado = $a - $b;
                break;
            case "*":
                $resultado = $a * $b;
                break;
            case "/":
                if ($b != 0) {
                    $resultado = $a / $b;
                } else {
                    $erro = "Não existe divisão por zero.";
                }
                break;
        }

    } else {
        $erro = "Digite apenas números válidos.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Calculadora PHP</title>
</head>
<body>

<h1>Minha Calculadora!</h1>

<form method="POST">
    A: <input type="text" name="a"><br><br>
    B: <input type="text" name="b"><br><br>

    Operação:
    <select name="operacao">
        <option value="+">Soma (+)</option>
        <option value="-">Subtração (-)</option>
        <option value="*">Multiplicação (*)</option>
        <option value="/">Divisão (/)</option>
    </select>
    <br><br>

    <input type="submit" value="Calcular">
</form>

<br>

<?php
if ($resultado !== null) {
    echo "Resultado: " . $resultado;
}

if ($erro !== null) {
    echo "<p style='color:red;'>$erro</p>";
}
?>

</body>
</html>
