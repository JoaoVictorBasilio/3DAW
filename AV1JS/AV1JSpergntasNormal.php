<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['tipo'] != 'normal') {
    header('Location: AV1JSlogin.php');
    exit;
}

function carregarPerguntas() {
    if (file_exists("perguntas.txt")) {
        $conteudo = file_get_contents("perguntas.txt");
        if (!empty($conteudo)) return json_decode($conteudo, true);
    }
    return array();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');
    $inputData = json_decode(file_get_contents('php://input'), true);
    
    if (!$inputData || empty($inputData['respostas'])) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar respostas.']);
        exit;
    }

    $resposta_json = json_encode([
        'email' => $_SESSION['email'],
        'data' => date('Y-m-d H:i:s'),
        'respostas' => $inputData['respostas']
    ], JSON_UNESCAPED_UNICODE);
    
    $conteudo = file_exists("respostas.txt") ? file_get_contents("respostas.txt") : '';
    if (!empty($conteudo) && substr($conteudo, -1) != "\n") $conteudo .= "\n";
    
    file_put_contents("respostas.txt", $conteudo . $resposta_json . "\n");
    echo json_encode(['status' => 'success', 'message' => 'Questionário enviado com sucesso!']);
    exit;
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
    <a href="AV1JSinicio.php" class="btn-voltar">← Voltar</a>
    <div id="mensagem-container"></div>

    <?php if (count($perguntas) > 0) { ?>
        <form id="formQuiz">
            <?php foreach ($perguntas as $index => $p) { ?>
                <div class="pergunta-box" data-id="<?php echo $p['id']; ?>" data-texto="<?php echo htmlspecialchars($p['texto']); ?>" data-tipo="<?php echo $p['tipo']; ?>">
                    <h3>Pergunta <?php echo $index + 1; ?> - <?php echo htmlspecialchars($p['texto']); ?></h3>
                    
                    <?php if ($p['tipo'] == 'multipla') { ?>
                        <div class="opcoes-multipla">
                            <?php foreach ($p['opcoes'] as $opcao) { ?>
                                <label>
                                    <input type="radio" name="resposta_<?php echo $p['id']; ?>" value="<?php echo htmlspecialchars($opcao); ?>">
                                    <?php echo htmlspecialchars($opcao); ?>
                                </label><br>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <textarea name="resposta_<?php echo $p['id']; ?>" style="width: 100%; height: 100px; padding: 8px; margin-top: 10px;"></textarea>
                    <?php } ?>
                </div>
            <?php } ?>
            <input type="submit" value="Enviar Respostas" class="btn-primary">
        </form>
    <?php } else { ?>
        <p>Nenhuma pergunta disponível no momento.</p>
    <?php } ?>
</div>

<script>
    document.getElementById('formQuiz').addEventListener('submit', function(e) {
        e.preventDefault();
        const pacotes = [];
        let temErro = false;

        document.querySelectorAll('.pergunta-box').forEach(box => {
            const id = box.getAttribute('data-id');
            const texto = box.getAttribute('data-texto');
            const tipo = box.getAttribute('data-tipo');
            let resp = '';

            if (tipo === 'multipla') {
                const radio = document.querySelector(`input[name="resposta_${id}"]:checked`);
                if (radio) resp = radio.value;
            } else {
                resp = document.querySelector(`textarea[name="resposta_${id}"]`).value.trim();
            }

            if (resp === '') temErro = true;
            pacotes.push({ pergunta_id: id, pergunta: texto, resposta: resp });
        });

        const msgBox = document.getElementById('mensagem-container');
        if (temErro) {
            msgBox.innerHTML = '<p class="msg-erro">Por favor, responda todas as perguntas!</p>';
            window.scrollTo(0, 0);
            return;
        }

        fetch('AV1JSperguntasNormal.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ respostas: pacotes })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                msgBox.innerHTML = `<p class="msg-sucesso">${data.message}</p> <a href="AV1JSinicio.php" class="btn-primary">Voltar ao Início</a>`;
                document.getElementById('formQuiz').style.display = 'none';
                window.scrollTo(0, 0);
            }
        });
    });
</script>
</body>
</html>