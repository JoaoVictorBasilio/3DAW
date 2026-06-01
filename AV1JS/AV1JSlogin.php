<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');
    
    $email = trim($_POST["email"]);
    $senha = trim($_POST["senha"]);
    $tipo = trim($_POST["tipo"]);

    if ($email === '' || $senha === '' || $tipo === '') {
        echo json_encode(['status' => 'error', 'message' => 'Preencha todos os campos!']);
        exit;
    }

    if (file_exists("usuarios.txt")) {
        $arq = fopen("usuarios.txt","r");
        while (($linha = fgets($arq)) !== false) {
            $linhaLimpa = trim($linha);
            if (empty($linhaLimpa)) continue;

            $dados = explode(";", $linhaLimpa);
            if (isset($dados[0]) && isset($dados[2]) && isset($dados[3])) {
                if ($dados[0] == $email && $dados[2] == $tipo && password_verify($senha, $dados[3])) {
                    $_SESSION['email'] = $email;
                    $_SESSION['tipo'] = $tipo;
                    echo json_encode(['status' => 'success', 'redirect' => 'AV1JSinicio.php']);
                    fclose($arq); 
                    exit;
                }
            }
        } 
        fclose($arq); 
        echo json_encode(['status' => 'error', 'message' => 'Email, senha ou tipo incorreto!']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Banco de usuários não encontrado.']);
        exit;
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

    <form id="formLogin">
        <label>Email:</label>
        <input type="email" name="email" id="email" required>
        
        <label>Senha:</label>
        <input type="password" name="senha" id="senha" required>

        <label>Tipo:</label>
        <select name="tipo">
            <option value="normal">Normal</option>
            <option value="adm">Administrador</option>
        </select>
        <br>
        <input type="submit" value="Login">
    </form>

    <div id="mensagem-container"></div>
    <a href="AV1JScadastro.php" style="display: inline-block; margin-top: 15px;">Não possui login?</a>
</div>

<script>
    document.getElementById('formLogin').addEventListener('submit', function(e) {
        e.preventDefault();
        const msgContainer = document.getElementById('mensagem-container');
        
        if (document.getElementById('senha').value.trim() === '') {
            msgContainer.innerHTML = `<p class="msg-erro">Digite sua senha!</p>`;
            return;
        }

        const formData = new FormData(this);
        fetch('AV1JSlogin.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                window.location.href = data.redirect;
            } else {
                msgContainer.innerHTML = `<p class="msg-erro">${data.message}</p>`;
            }
        });
    });
</script>
</body>
</html>