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
session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['tipo'])) {
    header('Location: AV1Login.php');
    exit;
}

$email = $_SESSION['email'];
$tipo = $_SESSION['tipo'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Início - Sistema de Questionário</title>
    <link rel="stylesheet" href="AV1.css">
</head>
<body>

<div class="container">
    <h1>Bem-vindo ao Sistema de Treinamento Corporativo</h1>
    
    <div class="info-usuario">
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    </div>

    <hr class="divisor">

    <?php if ($tipo == 'adm') { ?>
        <h2>Painel do Administrador</h2>
        <div class="menu-opcoes">
            <a href="AV1perguntasADM.php" class="btn-opcao">
                <strong>Gerenciar Perguntas</strong>
                <p>Criar, editar e excluir perguntas</p>
            </a>
            <a href="AV1verRespostas.php" class="btn-opcao">
                <strong>Ver Respostas dos Usuários</strong>
                <p>Visualizar respostas enviadas</p>
            </a>
        </div>
    <?php } else { ?>
        <h2>Começar Questionário</h2>
        <div class="menu-opcoes">
            <a href="AV1perguntasNormal.php" class="btn-opcao btn-primary">
                <strong>Iniciar Questionário</strong>
                <p>Responda o questionário e aprenda!</p>
            </a>
        </div>
    <?php } ?>

    <hr class="divisor">

    <a href="AV1logout.php" class="btn-logout">Sair</a>

</div>

</body>
</html>
