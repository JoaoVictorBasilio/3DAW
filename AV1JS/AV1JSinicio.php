<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['tipo'])) {
    header('Location: AV1JSlogin.php');
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
            <a href="AV1JSperguntasADM.php" class="btn-opcao">
                <strong>Gerenciar Perguntas</strong>
                <p>Criar, editar e excluir perguntas</p>
            </a>
            <a href="AV1JSverRespostas.php" class="btn-opcao">
                <strong>Ver Respostas dos Usuários</strong>
                <p>Visualizar respostas enviadas</p>
            </a>
        </div>
    <?php } else { ?>
        <h2>Começar Questionário</h2>
        <div class="menu-opcoes">
            <a href="AV1JSperguntasNormal.php" class="btn-opcao btn-primary">
                <strong>Iniciar Questionário</strong>
                <p>Responda o questionário e aprenda!</p>
            </a>
        </div>
    <?php } ?>

    <hr class="divisor">
    <a href="AV1JSlogout.php" class="btn-logout">Sair</a>
</div>
</body>
</html>