<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['tipo'] != 'adm') {
    header('Location: AV1JSlogin.php');
    exit;
}

$respostas = [];
if (file_exists("respostas.txt")) {
    $linhas = explode("\n", file_get_contents("respostas.txt"));
    foreach ($linhas as $linha) {
        $linha = trim($linha);
        if (!empty($linha)) {
            $resp = json_decode($linha, true);
            if ($resp) $respostas[] = $resp;
        }
    }
}
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
    <a href="AV1JSinicio.php" class="btn-voltar">← Voltar</a>
    <hr class="divisor">

    <?php if (count($respostas) > 0) { ?>
        <?php foreach ($respostas as $index => $resposta) { ?>
            <div class="resposta-box">
                <h3>Resposta #<?php echo $index + 1; ?></h3>
                <p><strong>Usuário:</strong> <?php echo htmlspecialchars($resposta['email']); ?></p>
                <p><strong>Data/Hora:</strong> <?php echo htmlspecialchars($resposta['data']); ?></p>
                <table>
                    <tr><th>Pergunta</th><th>Resposta do Usuário</th></tr>
                    <?php foreach ($resposta['respostas'] as $item) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['pergunta']); ?></td>
                            <td><?php echo htmlspecialchars($item['resposta']); ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>
    <?php } else { ?>
        <p>Nenhuma resposta registrada ainda.</p>
    <?php } ?>
</div>
</body>
</html>