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
    $nome = trim($_POST["nome"]);
    
    $nome = str_replace(';', ',', $nome); 

    $email = trim($_POST["email"]);
    $senha = trim($_POST["senha"]);
    $tipo = trim($_POST["tipo"]);


    if($nome === '' || $email === '' || $senha === '' || $tipo === ''){
        $msgErro = 'Erro: Preencha todos os campos!';
    } else if ($tipo == 'adm' && strpos($email, '@faeterj-rio.edu.br') === false) {
        $msgErro = 'Erro: Administradores devem usar email @faeterj-rio.edu.br!';
    } else {
        
        $emailExiste = false;

        if (file_exists("usuarios.txt")){
            $arq = fopen("usuarios.txt","r");
            if ($arq) {
                while(($linha = fgets($arq))!== false){
                    $linhaLimpa = trim($linha);
                    if (empty($linhaLimpa)) continue;

                    $dados = explode(";", $linhaLimpa);

                   
                    if(isset($dados[0]) && $dados[0] == $email){
                        $emailExiste = true;
                        break;
                    }
                }
                fclose($arq);
            }
        }

   
        if($emailExiste){
            $msgErro = "Erro: usuário já cadastrado!";
        } else {
         
            $senhaSegura = password_hash($senha, PASSWORD_DEFAULT);

           
            $conteudo = file_exists("usuarios.txt") ? file_get_contents("usuarios.txt") : '';
            $arq = fopen("usuarios.txt", "a") or die("Erro ao abrir o arquivo!");

            if(!empty($conteudo) && substr($conteudo, -1)!== "\n" && substr($conteudo, -1)!== "\r"){
                fwrite($arq, PHP_EOL);
            }

            $novaLinha = $email . ";" . $nome . ";" . $tipo . ";" . $senhaSegura . PHP_EOL;
            fwrite($arq, $novaLinha);

            fclose($arq);

            $msgSucesso = "Usuário cadastrado com sucesso!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang = "pt br">
    <head>
        <meta charset="UTF-8">
        <title>Cadastrar Usuário</title>
        <link rel="stylesheet" href="AV1.css">
</head>
<body>

<div class="container">
    <h1>Cadastro</h1>

    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Nome:</label>
        <input type="text" name="nome" required>

        <label>Tipo:</label>
        <select name="tipo">
            <option value="normal">Normal</option>
            <option value="adm">Administrador</option>
        </select>

        <label>Senha:</label>
        <input type="password" name="senha" required>
<br>
        <input type="submit" value="Cadastrar">
    </form>

<?php if($msgSucesso != "") { ?>
<p class="msg-sucesso"><?php echo $msgSucesso; ?></p>
<?php } ?>

<?php if($msgErro != "") { ?>
<p class="msg-erro"><?php echo $msgErro; ?></p>
<?php } ?>

<a href="AV1Login.php">Já possui login?</a>

</div>


</body>
</html>
