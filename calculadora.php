<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $a = (float)$_POST["a"];
    $b = (float)$_POST["b"];
    $operador = $_POST["operador"];
    $resultado = 0;

    if ($operador == "soma") {
        $resultado = $a + $b;

    } elseif ($operador == "sub") {
        $resultado = $a - $b;

    } elseif ($operador == "multi") {
        $resultado = $a * $b;

    } elseif ($operador == "divide") {

        if ($b == 0) {
            $erro = "Não pode dividir por zero";
        } else {
            $resultado = $a / $b;
        }

    } elseif ($operador == "elevar") {
        $resultado = pow($a, $b);

    } elseif ($operador == "raiz") {

        if ($b == 0) {
            $erro = "Índice da raiz não pode ser zero";
        } else {
            $resultado = pow($a, 1/$b);
        }

    } else {
        $erro = "Operador não definido";
    }
}
?>
<!DOCTYPE html>
<html>
<body>
<h1><?php echo 'Minha Calculadora!'; ?></h1>

<form method='POST' action='calculadora.php'>
    a:<input type=text name='a'><br>
    b:<input type=text name='b'>
    <br>Operação: 
    <br><input type="radio" name="operador" value="soma"> Soma
    <br><input type="radio" name="operador" value="sub"> Subtrai
    <br><input type="radio" name="operador" value="multi">Multiplica
    <br><input type="radio" name="operador" value="divide">Divide
    <br><input type="radio" name="operador" value="elevar"> Elevar
    <br><input type="radio" name="operador" value="raiz"> Raiz
    <br>
    <input type=submit value='Calcular'>
    <br><br>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if(isset($erro)){
        echo $erro;
    } else {
        echo 'Resultado: ' . $resultado;
    }

}
?>
    
</body>
</html>