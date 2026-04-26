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

if (!isset($_SESSION['email']) || $_SESSION['tipo'] != 'adm') {
    header('Location: AV1Login.php');
    exit;
}


function carregarRespostas() {
    $respostas = array();
    if (file_exists("respostas.txt")) {
        $conteudo = file_get_contents("respostas.txt");
        $linhas = explode("\n", $conteudo);
        
        foreach ($linhas as $linha) {
            $linha = trim($linha);
            if (!empty($linha)) {
                $resp = json_decode($linha, true);
                if ($resp) {
                    $respostas[] = $resp;
                }
            }
        }
    }
    return $respostas;
}

$respostas = carregarRespostas();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ver Respostas</title>
    <link rel="stylesheet" href="AV1.css">
</head>
<body>

<div class="container">
    <h1>Respostas dos Usuários</h1>
    
    <a href="AV1inicio.php" class="btn-voltar">← Voltar</a>

    <hr class="divisor">

    <?php if (count($respostas) > 0) { ?>
        <?php foreach ($respostas as $index => $resposta) { ?>
            <div class="resposta-box">
                <h3>Resposta #<?php echo $index + 1; ?></h3>
                <p><strong>Usuário:</strong> <?php echo htmlspecialchars($resposta['email']); ?></p>
                <p><strong>Data/Hora:</strong> <?php echo htmlspecialchars($resposta['data']); ?></p>
                
                <table>
                    <tr>
                        <th>Pergunta</th>
                        <th>Resposta do Usuário</th>
                    </tr>
                    <?php foreach ($resposta['respostas'] as $resp_item) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($resp_item['pergunta']); ?></td>
                            <td><?php echo htmlspecialchars($resp_item['resposta']); ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>
    <?php } else { ?>
        <p>Nenhuma resposta foi registrada ainda.</p>
    <?php } ?>

</div>

</body>
</html>
