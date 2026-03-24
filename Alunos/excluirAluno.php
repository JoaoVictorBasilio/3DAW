<?php


// Verifica se o parâmetro 'matricula' foi passado pela URL (via link da tabela)
if (isset($_GET["matricula"])) {
    $matricula = trim($_GET["matricula"]); // Pega a matrícula que precisa ser apagada

    // Verifica se o arquivo existe antes de tentar manuseá-lo
    if (file_exists("alunos.txt")) {
        // Abre o arquivo original (leitura) e cria um arquivo temporário (escrita)
        $arq = fopen("alunos.txt", "r");
        $temp = fopen("temp.txt", "w");

        // Lê o arquivo linha por linha
        while (($linha = fgets($arq)) !== false) {
            
            $linhaLimpa = trim($linha); // Remove sobras e quebras de linha
            if (empty($linhaLimpa)) continue; // Pula se a linha estiver vazia

            // Divide a linha nos pontos e vírgulas para analisar os campos
            $dados = explode(";", $linhaLimpa);

            // A lógica de exclusão consiste em: SE a matrícula for DIFERENTE da que queremos excluir...
            if ($dados[0] != $matricula) {
                fwrite($temp, $linhaLimpa . "\n");
            }
        }

        // Fecha os recursos de ambos os arquivos
        fclose($arq);
        fclose($temp);

        // Deleta o original e renomeia o temporário limpo para se tornar o original
        unlink("alunos.txt");
        rename("temp.txt", "alunos.txt");
    }

    // Redireciona o usuário de volta para a tela principal (lista) independentemente do resultado
    header("Location: listarAlunos.php");
    exit; // Interrompe o script para garantir que o redirecionamento ocorra imediatamente
}
?>