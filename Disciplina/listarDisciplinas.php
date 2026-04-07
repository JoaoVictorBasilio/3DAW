<?php

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Disciplinas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Lista de Disciplinas</h1>

    <form method="GET" style="margin-bottom: 12px;">
        <label for="q">Buscar disciplina (sigla/nome/carga):</label>
        <input type="text" id="q" name="q" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" placeholder="Digite o termo e pressione Enter" style="width:70%; margin-left:6px; padding:4px;" />
        <input type="submit" value="Buscar" style="margin-left:8px;" />
        <a href="listarDisciplinas.php" style="margin-left:8px;">Limpar</a>
    </form>

    <table>
    <tr>
        <th>Sigla</th>
        <th>Nome</th>
        <th>Carga Horária</th>
        <th>Ações</th>
    </tr>

    <?php
    // Confirma se o arquivo existe para evitar erro caso a lista ainda esteja vazia
    $busca = isset($_GET['q']) ? trim($_GET['q']) : '';

    if (file_exists("disciplinas.txt")) {
        // Abre o arquivo em modo de leitura ('r')
        $arq = fopen("disciplinas.txt", "r");

        // Lê o arquivo linha por linha
        while (($linha = fgets($arq)) !== false) {
            
            // Remove espaços e quebras de linha acidentais
            $linhaLimpa = trim($linha);

            // Se a linha estiver completamente vazia, pula para a próxima iteração
            if (empty($linhaLimpa)) {
                continue; 
            }

            // Divide a linha em um array (lista) de dados, cortando nos pontos e vírgulas
            $dados = explode(";", $linhaLimpa);

            // Verifica se a linha foi dividida corretamente em 3 partes e a sigla não está vazia
            if (count($dados) >= 3 && $dados[0] != "") {
                // Aplica filtro de busca se existir termo
                if ($busca !== '' && stripos($dados[0] . ' ' . $dados[1] . ' ' . $dados[2], $busca) === false) {
                    continue;
                }

                echo "<tr>";
                // htmlspecialchars previne bugs caso existam caracteres especiais no arquivo de texto
                echo "<td>" . htmlspecialchars($dados[0]) . "</td>";
                echo "<td>" . htmlspecialchars($dados[1]) . "</td>";
                echo "<td>" . htmlspecialchars($dados[2]) . "</td>";

                echo "<td>
                    <a href='alterarDisciplina.php?sigla=" . urlencode($dados[0]) . "'>Alterar</a> |
                    <a href='excluirDiscilplinas.php?sigla=" . urlencode($dados[0]) . "' onclick=\"return confirm('Tem certeza que deseja excluir esta disciplina?');\">Excluir</a>
                </td>";

                echo "</tr>";
            }
        }

        // Fecha a leitura do arquivo
        fclose($arq);
    }
    ?>

    </table>

    <br>
    <a href="incluirDisciplina.php">Adicionar nova disciplina</a>
</div>

</body>
</html>