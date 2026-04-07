<?php

$msg = ""; 
$msgErro = "";
$disciplina = null;


if (isset($_GET["sigla"])) {
    $siglaBusca = trim($_GET["sigla"]);

    // Só busca se o arquivo de fato existir
    if (file_exists("disciplinas.txt")) {
        $arq = fopen("disciplinas.txt", "r") or die("Erro ao abrir arquivo");

        // Lê o arquivo linha a linha de forma segura
        while (($linha = fgets($arq)) !== false) {
            $linhaLimpa = trim($linha);
            if (empty($linhaLimpa)) continue; // Ignora linhas em branco

            $dados = explode(";", $linhaLimpa);

            // Se a sigla na linha atual for igual à sigla buscada...
            if (isset($dados[0]) && $dados[0] == $siglaBusca) {
                $disciplina = $dados;
                break;
            }
        }

        fclose($arq);

        if (!$disciplina) {
            $msgErro = "Disciplina não encontrada para sigla $siglaBusca.";
        }
    } else {
        $msgErro = "Arquivo de disciplinas não encontrado.";
    }
} else {
    $msgErro = "Use a listagem de disciplinas para acessar a edição (clique em 'Alterar').";
}

// O que acontece ao clicar no botão 'Alterar' (Salvar os novos dados)
if (isset($_POST["alterar"])) {
    $sigla = trim($_POST["sigla"]);
    $nome = trim($_POST["nome"]);
    $carga = trim($_POST["carga"]);

    if (file_exists("disciplinas.txt")) {
        // Abre o original para ler, e cria um 'temp.txt' limpo para gravar
        $arq = fopen("disciplinas.txt", "r");
        $temp = fopen("temp.txt", "w");

        while (($linha = fgets($arq)) !== false) {
            $linhaLimpa = trim($linha);
            if (empty($linhaLimpa)) continue;

            $dados = explode(";", $linhaLimpa);

            // Se for a linha da disciplina que estamos editando, sobrescrevemos a variável da linha
            if ($dados[0] == $sigla) {
                $linhaLimpa = $sigla . ";" . $nome . ";" . $carga;
            }

            // Grava a linha no arquivo temporário com uma quebra de linha (\n)
            fwrite($temp, $linhaLimpa . "\n");
        }

        fclose($arq);
        fclose($temp);

        // Deleta o arquivo antigo
        unlink("disciplinas.txt");
        // Transforma o temporário (agora atualizado) no arquivo oficial
        rename("temp.txt", "disciplinas.txt");

        $msg = "Disciplina alterada com sucesso!";
        
        // Mantém as informações atualizadas na tela para o usuário ver o que salvou
        $disciplina = [$sigla, $nome, $carga]; 
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Disciplina</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Alterar Disciplina</h1>

    <?php if ($msgErro !== "") { ?>
        <p class="msg-erro"><?php echo htmlspecialchars($msgErro); ?></p>
    <?php } ?>

    <hr class="divisor">

    <?php if ($disciplina) { ?>
    <form method="POST">
        <label>Sigla (Não alterável):</label> 
        <input type="text" name="sigla" value="<?php echo htmlspecialchars($disciplina[0]); ?>" readonly>

        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($disciplina[1]); ?>" required>

        <label>Carga Horária:</label>
        <input type="number" name="carga" value="<?php echo htmlspecialchars($disciplina[2]); ?>" required>

        <input type="submit" name="alterar" value="Salvar Alterações">
    </form>
    <?php } ?>

    <?php if ($msg != "") { ?>
        <p class="msg-sucesso"><?php echo $msg; ?></p>
    <?php } ?>

    <br>
    <a href="listarDisciplinas.php">Voltar para a lista</a>
</div>

</body>
</html>