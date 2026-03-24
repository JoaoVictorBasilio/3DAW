<?php
// Trabalho acadêmico: Sistema de CRUD de alunos usando arquivo texto (alunos.txt)
// Autor: [Seu Nome] - RA: [Seu RA]
// Curso: [Seu Curso]
// Disciplina: [Sua Disciplina]
// Professor(a): [Nome do Professor]
// Data: 
$msgSucesso = ""; 
$msgErro = "";   

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matricula = trim($_POST["matricula"]);
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);

    // Validação básica: campos obrigatórios
    if ($matricula === '' || $nome === '' || $email === '') {
        $msgErro = 'Erro: Preencha todos os campos.';
    } else {
        $matriculaExiste = false; // Flag para controlar se achamos duplicata

        // Passo 1: Verifica se o arquivo existe para procurar por duplicatas
        if (file_exists("alunos.txt")) {
            $arq = fopen("alunos.txt", "r");

            // Lê o arquivo linha a linha
            while (($linha = fgets($arq)) !== false) {
                $linhaLimpa = trim($linha);
                if (empty($linhaLimpa)) continue;

                $dados = explode(";", $linhaLimpa);

                // Compara a matrícula da linha atual com a que o usuário digitou
                if (isset($dados[0]) && $dados[0] == $matricula) {
                    $matriculaExiste = true; // Achou!
                    break; // Interrompe o loop
                }
            }
            fclose($arq);
        }

        // Passo 2: Decide o que fazer com base na verificação
        if ($matriculaExiste) {
            $msgErro = "Erro: A matrícula '$matricula' já está cadastrada!";
        } else {
            
            $conteudo = file_exists("alunos.txt") ? file_get_contents("alunos.txt") : '';
            $arq = fopen("alunos.txt", "a") or die("Erro ao abrir arquivo");

            // Verifica se o arquivo não está vazio e se o último caractere NÃO é uma quebra de linha
            if (!empty($conteudo) && substr($conteudo, -1) !== "\n" && substr($conteudo, -1) !== "\r") {
                // Força um "Enter" (quebra de linha) antes de adicionar
                fwrite($arq, PHP_EOL);
            }

            // Monta a linha do novo aluno 
            $novaLinha = $matricula . ";" . $nome . ";" . $email . PHP_EOL;
            fwrite($arq, $novaLinha);

            fclose($arq);

            $msgSucesso = "Aluno cadastrado com sucesso!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Incluir Aluno</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Incluir Aluno</h1>

    <form method="POST">
        <label>Matrícula:</label> 
        <input type="text" name="matricula" required>
        
        <label>Nome:</label> 
        <input type="text" name="nome" required>
        
        <label>Email:</label> 
        <input type="email" name="email" required>

        <input type="submit" value="Cadastrar">
    </form>

    <?php if ($msgSucesso != "") { ?>
        <p class="msg-sucesso"><?php echo $msgSucesso; ?></p>
    <?php } ?>

    <?php if ($msgErro != "") { ?>
        <p class="msg-erro"><?php echo $msgErro; ?></p>
    <?php } ?>

    <br>
    <a href="listarAlunos.php">Ver lista de alunos</a>
</div>

</body>
</html>