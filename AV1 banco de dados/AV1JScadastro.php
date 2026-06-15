<?php
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');
    
    $nome = trim($_POST["nome"]);
    $nome = str_replace(';', ',', $nome); 
    $email = trim($_POST["email"]);
    $senha = trim($_POST["senha"]);
    $tipo = trim($_POST["tipo"]);

    if ($nome === '' || $email === '' || $senha === '' || $tipo === '') {
        echo json_encode(['status' => 'error', 'message' => 'Erro: Preencha todos os campos!']);
        exit;
    } 
    
    if ($tipo == 'adm' && strpos($email, '@faeterj-rio.edu.br') === false) {
        echo json_encode(['status' => 'error', 'message' => 'Erro: Administradores devem usar email @faeterj-rio.edu.br!']);
        exit;
    }

    $emailExiste = false;
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Erro: usuário já cadastrado!']);
        exit;
    } else {
        $senhaSegura = password_hash($senha, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$nome, $email, $senhaSegura, $tipo])) {
            echo json_encode(['status' => 'success', 'message' => 'Usuário cadastrado com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar no banco de dados.']);
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="AV1.css">
</head>
<body>

<div class="container">
    <h1>Cadastro</h1>

    <form id="formCadastro">
        <label>Email:</label>
        <input type="email" id="email" name="email" required>

        <label>Nome:</label>
        <input type="text" id="nome" name="nome" required>

        <label>Tipo:</label>
        <select id="tipo" name="tipo">
            <option value="normal">Normal</option>
            <option value="adm">Administrador</option>
        </select>

        <label>Senha:</label>
        <input type="password" id="senha" name="senha" required>
        <br>
        <input type="submit" value="Cadastrar">
    </form>

    <div id="mensagem-container"></div>
    <a href="AV1JSlogin.php" style="display: inline-block; margin-top: 15px;">Já possui login?</a>
</div>

<script>
    document.getElementById('formCadastro').addEventListener('submit', function(e) {
        e.preventDefault();
        const msgContainer = document.getElementById('mensagem-container');
        
        const senha = document.getElementById('senha').value;
        const tipo = document.getElementById('tipo').value;
        const email = document.getElementById('email').value;

        if (senha.length < 4) {
            msgContainer.innerHTML = `<p class="msg-erro">A senha deve ter pelo menos 4 caracteres.</p>`;
            return;
        }

        if (tipo === 'adm' && !email.includes('@faeterj-rio.edu.br')) {
            msgContainer.innerHTML = `<p class="msg-erro">Administradores devem usar email da faeterj.</p>`;
            return;
        }

        const formData = new FormData(this);
        fetch('AV1JScadastro.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                msgContainer.innerHTML = `<p class="msg-sucesso">${data.message}</p>`;
                this.reset();
            } else {
                msgContainer.innerHTML = `<p class="msg-erro">${data.message}</p>`;
            }
        });
    });
</script>
</body>
</html>
