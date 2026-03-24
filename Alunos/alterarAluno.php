<?php

$msg = ""; 
$msgErro = "";
$aluno = null;


if (isset($_GET["matricula"])) {
    $matriculaBusca = trim($_GET["matricula"]);

    // Só busca se o arquivo de fato existir
    if (file_exists("alunos.txt")) {
        $arq = fopen("alunos.txt", "r") or die("Erro ao abrir arquivo");

        // Lê o arquivo linha a linha de forma segura
        while (($linha = fgets($arq)) !== false) {
            $linhaLimpa = trim($linha);
            if (empty($linhaLimpa)) continue; // Ignora linhas em branco

            $dados = explode(";", $linhaLimpa);

            // Se a matrícula na linha atual for igual à matrícula buscada...
            if (isset($dados[0]) && $dados[0] == $matriculaBusca) {
                $aluno = $dados;
                break;
            }
        }

        fclose($arq);

        if (!$aluno) {
            $msgErro = "Aluno não encontrado para matrícula $matriculaBusca.";
        }
    } else {
        $msgErro = "Arquivo de alunos não encontrado.";
    }
} else {
    $msgErro = "Use a listagem de alunos para acessar a edição (clique em 'Alterar').";
}

// O que acontece ao clicar no botão 'Alterar' (Salvar os novos dados)
if (isset($_POST["alterar"])) {
    $matricula = trim($_POST["matricula"]);
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);

    if (file_exists("alunos.txt")) {
        // Abre o original para ler, e cria um 'temp.txt' limpo para gravar
        $arq = fopen("alunos.txt", "r");
        $temp = fopen("temp.txt", "w");

        while (($linha = fgets($arq)) !== false) {
            $linhaLimpa = trim($linha);
            if (empty($linhaLimpa)) continue;

            $dados = explode(";", $linhaLimpa);

            // Se for a linha do aluno que estamos editando, sobrescrevemos a variável da linha
            if ($dados[0] == $matricula) {
                $linhaLimpa = $matricula . ";" . $nome . ";" . $email;
            }

            // Grava a linha no arquivo temporário com uma quebra de linha (\n)
            fwrite($temp, $linhaLimpa . "\n");
        }

        fclose($arq);
        fclose($temp);

        // Deleta o arquivo antigo
        unlink("alunos.txt");
        // Transforma o temporário (agora atualizado) no arquivo oficial
        rename("temp.txt", "alunos.txt");

        $msg = "Aluno alterado com sucesso!";
        
        // Mantém as informações atualizadas na tela para o usuário ver o que salvou
        $aluno = [$matricula, $nome, $email]; 
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Aluno</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Alterar Aluno</h1>

    <?php if ($msgErro !== "") { ?>
        <p class="msg-erro"><?php echo htmlspecialchars($msgErro); ?></p>
    <?php } ?>

    <hr class="divisor">

    <?php if ($aluno) { ?>
    <form method="POST">
        <label>Matrícula (Não alterável):</label> 
        <input type="text" name="matricula" value="<?php echo htmlspecialchars($aluno[0]); ?>" readonly>

        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($aluno[1]); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($aluno[2]); ?>" required>

        <input type="submit" name="alterar" value="Salvar Alterações">
    </form>
    <?php } ?>

    <?php if ($msg != "") { ?>
        <p class="msg-sucesso"><?php echo $msg; ?></p>
    <?php } ?>

    <br>
    <a href="listarAlunos.php">Voltar para a lista</a>
</div>

</body>
</html>