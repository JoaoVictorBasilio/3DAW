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

$msgSucesso = "";
$msgErro = "";
$perguntas = array();
$editando = false;
$pergunta_edicao = null;


function carregarPerguntas() {
    if (file_exists("perguntas.txt")) {
        $conteudo = file_get_contents("perguntas.txt");
        if (!empty($conteudo)) {
            return json_decode($conteudo, true);
        }
    }
    return array();
}


function salvarPerguntas($perguntas) {
    file_put_contents("perguntas.txt", json_encode($perguntas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $acao = isset($_POST['acao']) ? $_POST['acao'] : '';
    $perguntas = carregarPerguntas();

    if ($acao == 'adicionar') {
        $pergunta_texto = trim($_POST['pergunta_texto'] ?? '');
        $tipo = trim($_POST['tipo'] ?? '');
        
        if (empty($pergunta_texto) || empty($tipo)) {
            $msgErro = "Preencha todos os campos!";
        } else {
            $nova_pergunta = array(
                'id' => time(),
                'texto' => $pergunta_texto,
                'tipo' => $tipo,
                'opcoes' => array(),
                'resposta_correta' => ''
            );

            if ($tipo == 'multipla') {
                for ($i = 1; $i <= 4; $i++) {
                    $opcao = trim($_POST['opcao_' . $i] ?? '');
                    if (!empty($opcao)) {
                        $nova_pergunta['opcoes'][] = $opcao;
                    }
                }
                $nova_pergunta['resposta_correta'] = trim($_POST['resposta_correta'] ?? '');

                if (count($nova_pergunta['opcoes']) < 2) {
                    $msgErro = "Adicione pelo menos 2 opções para perguntas de múltipla escolha!";
                } else {
                    $perguntas[] = $nova_pergunta;
                    salvarPerguntas($perguntas);
                    $msgSucesso = "Pergunta adicionada com sucesso!";
                }
            } else {
                $perguntas[] = $nova_pergunta;
                salvarPerguntas($perguntas);
                $msgSucesso = "Pergunta adicionada com sucesso!";
            }
        }
    } elseif ($acao == 'editar') {
        $id = intval($_POST['id'] ?? 0);
        $pergunta_texto = trim($_POST['pergunta_texto'] ?? '');
        $tipo = trim($_POST['tipo'] ?? '');
        
        if (empty($pergunta_texto) || empty($tipo)) {
            $msgErro = "Preencha todos os campos!";
        } else {
            foreach ($perguntas as &$p) {
                if ($p['id'] == $id) {
                    $p['texto'] = $pergunta_texto;
                    $p['tipo'] = $tipo;
                    $p['opcoes'] = array();
                    $p['resposta_correta'] = '';
                    
                    if ($tipo == 'multipla') {
                        for ($i = 1; $i <= 4; $i++) {
                            $opcao = trim($_POST['opcao_' . $i] ?? '');
                            if (!empty($opcao)) {
                                $p['opcoes'][] = $opcao;
                            }
                        }
                        $p['resposta_correta'] = trim($_POST['resposta_correta'] ?? '');
                        
                        if (count($p['opcoes']) < 2) {
                            $msgErro = "Adicione pelo menos 2 opções para perguntas de múltipla escolha!";
                            break;
                        }
                    }
                    
                    if (empty($msgErro)) {
                        $msgSucesso = "Pergunta alterada com sucesso!";
                    }
                    break;
                }
            }
            
            if (empty($msgErro)) {
                salvarPerguntas($perguntas);
            }
        }
    } elseif ($acao == 'excluir') {
        $id = intval($_POST['id'] ?? 0);
        $perguntas = array_filter($perguntas, function($p) use ($id) {
            return $p['id'] != $id;
        });
        $perguntas = array_values($perguntas);
        salvarPerguntas($perguntas);
        $msgSucesso = "Pergunta excluída com sucesso!";
    }
}


if (isset($_GET['editar'])) {
    $perguntas = carregarPerguntas();
    $id_editar = intval($_GET['editar']);
    foreach ($perguntas as $p) {
        if ($p['id'] == $id_editar) {
            $editando = true;
            $pergunta_edicao = $p;
            break;
        }
    }
}

$perguntas = carregarPerguntas();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Perguntas</title>
    <link rel="stylesheet" href="AV1.css">
</head>
<body>

<div class="container">
    <h1>Gerenciar Perguntas</h1>
    
    <a href="AV1inicio.php" class="btn-voltar">← Voltar</a>

    <?php if ($msgSucesso) { ?>
        <p class="msg-sucesso"><?php echo $msgSucesso; ?></p>
    <?php } ?>
    
    <?php if ($msgErro) { ?>
        <p class="msg-erro"><?php echo $msgErro; ?></p>
    <?php } ?>

    <hr class="divisor">

    <h2><?php echo $editando ? 'Editar Pergunta' : 'Adicionar Nova Pergunta'; ?></h2>
    <form method="POST">
        <input type="hidden" name="acao" value="<?php echo $editando ? 'editar' : 'adicionar'; ?>">
        <?php if ($editando) { ?>
            <input type="hidden" name="id" value="<?php echo $pergunta_edicao['id']; ?>">
        <?php } ?>

        <label>Pergunta:</label>
        <textarea name="pergunta_texto" required style="width: 100%; padding: 8px; margin-bottom: 10px;"><?php echo $editando ? htmlspecialchars($pergunta_edicao['texto']) : ''; ?></textarea>

        <label>Tipo:</label>
        <select name="tipo" id="tipo_pergunta" onchange="mostrarOpcoes()">
            <option value="multipla" <?php echo ($editando && $pergunta_edicao['tipo'] == 'multipla') ? 'selected' : ''; ?>>Múltipla Escolha</option>
            <option value="discursiva" <?php echo ($editando && $pergunta_edicao['tipo'] == 'discursiva') ? 'selected' : ''; ?>>Discursiva</option>
        </select>

        <div id="opcoes_multipla" style="display: <?php echo (!$editando || $pergunta_edicao['tipo'] == 'multipla') ? 'block' : 'none'; ?>;">
            <p><strong>Opções de Resposta:</strong></p>
            <?php for ($i = 1; $i <= 4; $i++) { ?>
                <input type="text" name="opcao_<?php echo $i; ?>" placeholder="Opção <?php echo $i; ?>" 
                       value="<?php echo $editando && isset($pergunta_edicao['opcoes'][$i-1]) ? htmlspecialchars($pergunta_edicao['opcoes'][$i-1]) : ''; ?>">
            <?php } ?>

            <label>Resposta Correta:</label>
            <input type="text" name="resposta_correta" placeholder="Digite a resposta correta" 
                   value="<?php echo $editando ? htmlspecialchars($pergunta_edicao['resposta_correta']) : ''; ?>">
        </div>

        <input type="submit" value="<?php echo $editando ? 'Atualizar Pergunta' : 'Adicionar Pergunta'; ?>">
        <?php if ($editando) { ?>
            <a href="AV1perguntasADM.php" style="padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px; display: inline-block;">Cancelar</a>
        <?php } ?>
    </form>

    <hr class="divisor">

    <h2>Perguntas Cadastradas</h2>
    <?php if (count($perguntas) > 0) { ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Pergunta</th>
                <th>Tipo</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($perguntas as $index => $pergunta) { ?>
                <tr>
                    <td><?php echo $pergunta['id']; ?></td>
                    <td><?php echo htmlspecialchars($pergunta['texto']); ?></td>
                    <td><?php echo $pergunta['tipo'] == 'multipla' ? 'Múltipla Escolha' : 'Discursiva'; ?></td>
                    <td>
                        <a href="?editar=<?php echo $pergunta['id']; ?>" style="padding: 5px 10px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin-right: 5px;">Editar</a>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="acao" value="excluir">
                            <input type="hidden" name="id" value="<?php echo $pergunta['id']; ?>">
                            <input type="submit" value="Excluir" onclick="return confirm('Tem certeza?');" style="padding: 5px 10px; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        </form>
                    </td>
                </tr>
                <?php if ($pergunta['tipo'] == 'multipla') { ?>
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="4">
                            <strong>Opções:</strong> 
                            <?php echo implode(", ", array_map('htmlspecialchars', $pergunta['opcoes'])); ?>
                            <br>
                            <strong>Resposta Correta:</strong> <?php echo htmlspecialchars($pergunta['resposta_correta']); ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>Nenhuma pergunta cadastrada.</p>
    <?php } ?>

</div>

<script>
function mostrarOpcoes() {
    var tipo = document.getElementById('tipo_pergunta').value;
    var opcoes = document.getElementById('opcoes_multipla');
    if (tipo == 'multipla') {
        opcoes.style.display = 'block';
    } else {
        opcoes.style.display = 'none';
    }
}
</script>

</body>
</html>
