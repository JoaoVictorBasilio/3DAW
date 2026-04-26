<!--O Sr. Water Falls precisa de um sistema de jogo corporativo, para treinar seus gestores em 
situações difíceis. O jogo deverá gerenciar situações de perguntas e respostas (decisões) 
encadeadas.
O game é composto por vários desafios e cada desafio tem um objetivo específico, como por 
exemplo, gerenciar o andamento de um projeto, resolver um problema administrativo, contratar 
um novo funcionário, conceder um empréstimo e outros.
Neste primeiro momento será desenvolvido somente o cadastro Usuários, Perguntas e Respostas.
Criar as funcionalidades de Criar Perguntas e respostas de multipla escolha, Criar Perguntas e 
respostas de texto,  alterar Perguntas e suas respostas de multipla escolha, listar todas 
Perguntas, listar uma Pergunta e excluir Pergunta e respostas.
Inicialmente usaremos arquivos texto(txt) para salvar os usuários.
As funcionalidades de Perguntas e respostas devem estar disponíveis por tela.
O código deverá ser em PHP.
Então deverá ser criado:
1. Criar Perguntas e respostas de multipla escolha.
2.Criar Perguntas e respostas de texto.
3. Alterar Perguntas e suas respostas de multipla escolha
4. Alterar Perguntas com respostas de texto
5. Listar Perguntas e repostas.
6. Listar uma Pergunta.
7. Excluir Pergunta e respostas-->

<?php

$msgSucesso = "";
$msgErro = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = trim($_POST["email"]);
    $senha = trim($_POST["senha"]);
    $tipo = trim($_POST["tipo"]);

    if($email === '' || $senha === '' || $tipo === ''){
        $msgErro = 'Erro: Preencha todos os campos!';
    } else {
        $Login = false;

        if(file_exists("usuarios.txt")){
            $arq = fopen("usuarios.txt","r");

            if ($arq) {
                
             
                while(($linha = fgets($arq)) !== false){
                    $linhaLimpa = trim($linha);
                    if (empty($linhaLimpa)) continue;

                    $dados = explode(";", $linhaLimpa);

                    if(isset($dados[0]) && isset($dados[2]) && isset($dados[3])){
                        if($dados[0] == $email && $dados[2] == $tipo && password_verify($senha, $dados[3])){
                            $Login = true;
                            break; 
                        }
                    }
                } 
            
                fclose($arq); 
            }

            if($Login){
                session_start();
                $_SESSION['email'] = $email;
                $_SESSION['tipo'] = $tipo;
                header('Location: AV1inicio.php');
                exit;
            } else {
                $msgErro = "Email, senha ou tipo incorreto!";
            }
        } else {
            $msgErro = "Erro: Banco de usuários não encontrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="AV1.css">
</head>
<body>

<div class="container">
    <h1>Login</h1>

    <form method="POST" autocomplete="on">
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <label>Senha:</label>
        <input type="password" name="senha" required>

        <label>Tipo:</label>
        <select name="tipo">
            <option value="normal">Normal</option>
            <option value="adm">Administrador</option>
        </select>
        <br>
        <input type="submit" value="Login">
</form>

<?php if($msgSucesso != "") { ?>
<p class="msg-sucesso"><?php echo $msgSucesso; ?></p>
<?php } ?>

<?php if($msgErro != "") { ?>
<p class="msg-erro"><?php echo $msgErro; ?></p>
<?php } ?>

<a href="AV1Cadastro.php">Não possui login?</a>

</div>

</body>
</html>