<?php
$msgSucesso = "";
$msgErro = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $sigla = trim($_POST["sigla"]);
    $nome = trim($_POST["nome"]);
    $carga = trim($_POST["carga"]);

    if($sigla === '' || $nome === '' || $carga === ''){
        $msgErro ='Erro: Preencha todos os campos.';
    } else {
        $siglaExiste = false;

        if(file_exists("disciplinas.txt")){
            $arq = fopen("disciplinas.txt", "r");

            while (($linha = fgets($arq)) !== false) {
                $linhaLimpa = trim($linha);
                if (empty($linhaLimpa)) continue;
                
                $dados = explode(";",$linhaLimpa);

                if (isset($dados[0]) && $dados[0] == $sigla) {$siglaExiste = true;
                break;
                }
            }
            fclose($arq);
        }
        if($siglaExiste) {
            $msgErro = "Erro: A sigla '$sigla' já está cadastrada!";
        } else {
            $conteudo = file_exists("disciplinas.txt") ? file_get_contents("disciplina.txt") : '';
            $arq = fopen("disciplinas.txt", "a") or die("Erro ao abrir arquivo");
            if (!empty($conteudo) && substr($conteudo, -1) !== "\n" && substr($conteudo, -1) !== "\r") {
                fwrite($arq, PHP_EOL);
            }
            $novaLinha = $sigla . ";" . $nome . ";" . $carga . ":" . PHP_EOL;
            fwrite($arq, $novaLinha);
            fclose($arq);
            $msgSucesso = "Disciplina cadastrada com sucesso!";



    }
}
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Incluir Disciplina</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Incluir Disciplina</h1>

    <form method="POST">
        <label>Sigla:</label> 
        <input type="text" name="sigla" required>
        
        <label>Nome:</label> 
        <input type="text" name="nome" required>
        
        <label>Email:</label> 
        <input type="carga" name="carga" required>

        <input type="submit" value="Cadastrar">
    </form>

    <?php if ($msgSucesso != "") { ?>
        <p class="msg-sucesso"><?php echo $msgSucesso; ?></p>
    <?php } ?>

    <?php if ($msgErro != "") { ?>
        <p class="msg-erro"><?php echo $msgErro; ?></p>
    <?php } ?>

    <br>


</body>
</html>