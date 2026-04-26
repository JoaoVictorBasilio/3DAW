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

if (!isset($_SESSION['email']) || $_SESSION['tipo'] != 'normal') {
    header('Location: AV1Login.php');
    exit;
}

$email = $_SESSION['email'];
$msgSucesso = "";
$msgErro = "";


function carregarPerguntas() {
    if (file_exists("perguntas.txt")) {
        $conteudo = file_get_contents("perguntas.txt");
        if (!empty($conteudo)) {
            return json_decode($conteudo, true);
        }
    }
    return array();
}


function salvarResposta($email, $respostas) {
    $data_hora = date('Y-m-d H:i:s');
    $resposta_json = json_encode(array(
        'email' => $email,
        'data' => $data_hora,
        'respostas' => $respostas
    ));
    
    $conteudo = file_exists("respostas.txt") ? file_get_contents("respostas.txt") : '';
    if (!empty($conteudo) && substr($conteudo, -1) != "\n") {
        $conteudo .= "\n";
    }
    file_put_contents("respostas.txt", $conteudo . $resposta_json . "\n", FILE_APPEND);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar_respostas'])) {
    $perguntas = carregarPerguntas();
    
    if (count($perguntas) == 0) {
        $msgErro = "Nenhuma pergunta disponível!";
    } else {
        $respostas = array();
        $todas_respondidas = true;

        foreach ($perguntas as $pergunta) {
            $id = $pergunta['id'];
            $resposta = isset($_POST['resposta_' . $id]) ? trim($_POST['resposta_' . $id]) : '';
            
            if (empty($resposta)) {
                $todas_respondidas = false;
                break;
            }
            
            $respostas[] = array(
                'pergunta_id' => $id,
                'pergunta' => $pergunta['texto'],
                'resposta' => $resposta
            );
        }

        if (!$todas_respondidas) {
            $msgErro = "Por favor, responda todas as perguntas!";
        } else {
            salvarResposta($email, $respostas);
            $msgSucesso = "Questionário enviado com sucesso! Obrigado por participar.";
        }
    }
}

$perguntas = carregarPerguntas();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Responder Questionário</title>
    <link rel="stylesheet" href="AV1.css">
</head>
<body>

<div class="container">
    <h1>Questionário de Treinamento</h1>
    
    <a href="AV1inicio.php" class="btn-voltar">← Voltar</a>

    <?php if ($msgSucesso) { ?>
        <p class="msg-sucesso"><?php echo $msgSucesso; ?></p>
        <a href="AV1inicio.php" class="btn-primary">Voltar ao Início</a>
    <?php } else { ?>
        
        <?php if ($msgErro) { ?>
            <p class="msg-erro"><?php echo $msgErro; ?></p>
        <?php } ?>

        <?php if (count($perguntas) > 0) { ?>
            <form method="POST">
                <?php foreach ($perguntas as $index => $pergunta) { ?>
                    <div class="pergunta-box">
                        <h3>Pergunta <?php echo $index + 1; ?> - <?php echo htmlspecialchars($pergunta['texto']); ?></h3>
                        
                        <?php if ($pergunta['tipo'] == 'multipla') { ?>
                            <div class="opcoes-multipla">
                                <?php foreach ($pergunta['opcoes'] as $opcao_index => $opcao) { ?>
                                    <label>
                                        <input type="radio" name="resposta_<?php echo $pergunta['id']; ?>" 
                                               value="<?php echo htmlspecialchars($opcao); ?>" required>
                                        <?php echo htmlspecialchars($opcao); ?>
                                    </label>
                                    <br>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <textarea name="resposta_<?php echo $pergunta['id']; ?>" 
                                      placeholder="Digite sua resposta aqui..." 
                                      style="width: 100%; height: 100px; padding: 8px; margin-top: 10px;" 
                                      required></textarea>
                        <?php } ?>
                    </div>
                <?php } ?>

                <input type="hidden" name="enviar_respostas" value="1">
                <input type="submit" value="Enviar Respostas" class="btn-primary">
            </form>
        <?php } else { ?>
            <p>Nenhuma pergunta disponível no momento. Tente mais tarde!</p>
        <?php } ?>
    <?php } ?>

</div>

</body>
</html>
