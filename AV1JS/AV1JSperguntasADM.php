<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['tipo'] != 'adm') {
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

function salvarPerguntas($perguntas) {
    file_put_contents("perguntas.txt", json_encode($perguntas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');
    $acao = $_POST['acao'] ?? '';
    $perguntas = carregarPerguntas();

    if ($acao == 'excluir') {
        $id = intval($_POST['id'] ?? 0);
        $perguntas = array_values(array_filter($perguntas, fn($p) => $p['id'] != $id));
        salvarPerguntas($perguntas);
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($acao == 'adicionar' || $acao == 'editar') {
        $texto = trim($_POST['pergunta_texto'] ?? '');
        $tipo = trim($_POST['tipo'] ?? '');
        
        $opcoes = [];
        $correta = '';
        if ($tipo == 'multipla') {
            for ($i = 1; $i <= 4; $i++) {
                if (!empty($_POST['opcao_'.$i])) $opcoes[] = trim($_POST['opcao_'.$i]);
            }
            $correta = trim($_POST['resposta_correta'] ?? '');
        }

        if ($acao == 'adicionar') {
            $perguntas[] = [
                'id' => time(), 'texto' => $texto, 'tipo' => $tipo, 
                'opcoes' => $opcoes, 'resposta_correta' => $correta
            ];
        } else {
            $id = intval($_POST['id']);
            foreach ($perguntas as &$p) {
                if ($p['id'] == $id) {
                    $p['texto'] = $texto; $p['tipo'] = $tipo; 
                    $p['opcoes'] = $opcoes; $p['resposta_correta'] = $correta;
                    break;
                }
            }
        }
        salvarPerguntas($perguntas);
        echo json_encode(['status' => 'success']);
        exit;
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
    <a href="AV1JSinicio.php" class="btn-voltar">← Voltar</a>
    <div id="mensagem-container"></div>

    <hr class="divisor">

    <h2 id="titulo-form">Adicionar Nova Pergunta</h2>
    <form id="formPergunta">
        <input type="hidden" name="acao" id="acao" value="adicionar">
        <input type="hidden" name="id" id="id_pergunta" value="">

        <label>Pergunta:</label>
        <textarea name="pergunta_texto" id="pergunta_texto" required style="width: 100%; padding: 8px; margin-bottom: 10px;"></textarea>

        <label>Tipo:</label>
        <select name="tipo" id="tipo_pergunta" onchange="mostrarOpcoes()">
            <option value="multipla">Múltipla Escolha</option>
            <option value="discursiva">Discursiva</option>
        </select>

        <div id="opcoes_multipla">
            <p><strong>Opções de Resposta:</strong></p>
            <input type="text" name="opcao_1" id="opcao_1" placeholder="Opção 1">
            <input type="text" name="opcao_2" id="opcao_2" placeholder="Opção 2">
            <input type="text" name="opcao_3" id="opcao_3" placeholder="Opção 3">
            <input type="text" name="opcao_4" id="opcao_4" placeholder="Opção 4">
            <label>Resposta Correta:</label>
            <input type="text" name="resposta_correta" id="resposta_correta" placeholder="Digite a resposta correta">
        </div>

        <input type="submit" value="Salvar Pergunta" id="btn-submit">
        <button type="button" onclick="cancelarEdicao()" id="btn-cancelar" style="display:none; padding:12px; margin-left:10px;">Cancelar</button>
    </form>

    <hr class="divisor">
    <h2>Perguntas Cadastradas</h2>
    <?php if (count($perguntas) > 0) { ?>
        <table>
            <tr><th>ID</th><th>Pergunta</th><th>Tipo</th><th>Ações</th></tr>
            <?php foreach ($perguntas as $p) { ?>
                <tr id="linha-<?php echo $p['id']; ?>">
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo htmlspecialchars($p['texto']); ?></td>
                    <td><?php echo $p['tipo']; ?></td>
                    <td>
                        <button onclick='editar(<?php echo json_encode($p); ?>)' style="background:#007bff; color:white; border:none; padding:5px; cursor:pointer; border-radius:4px;">Editar</button>
                        <button onclick="excluir(<?php echo $p['id']; ?>)" style="background:#dc3545; color:white; border:none; padding:5px; cursor:pointer; border-radius:4px;">Excluir</button>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { echo "<p>Nenhuma pergunta cadastrada.</p>"; } ?>
</div>

<script>
    function mostrarOpcoes() {
        document.getElementById('opcoes_multipla').style.display = 
            document.getElementById('tipo_pergunta').value == 'multipla' ? 'block' : 'none';
    }

    document.getElementById('formPergunta').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const tipo = document.getElementById('tipo_pergunta').value;
        if(tipo === 'multipla') {
            const op1 = document.getElementById('opcao_1').value.trim();
            const op2 = document.getElementById('opcao_2').value.trim();
            if(op1 === '' || op2 === '') {
                alert('Questões de múltipla escolha precisam de pelo menos 2 opções.');
                return;
            }
        }

        const formData = new FormData(this);
        fetch('AV1JSperguntasADM.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') location.reload();
        });
    });

    function editar(pergunta) {
        document.getElementById('titulo-form').innerText = "Editar Pergunta";
        document.getElementById('acao').value = "editar";
        document.getElementById('id_pergunta').value = pergunta.id;
        document.getElementById('pergunta_texto').value = pergunta.texto;
        document.getElementById('tipo_pergunta').value = pergunta.tipo;
        
        if (pergunta.tipo === 'multipla') {
            for(let i=1; i<=4; i++) {
                document.getElementById('opcao_'+i).value = pergunta.opcoes[i-1] || '';
            }
            document.getElementById('resposta_correta').value = pergunta.resposta_correta;
        }
        mostrarOpcoes();
        document.getElementById('btn-cancelar').style.display = 'inline-block';
        window.scrollTo(0,0);
    }

    function cancelarEdicao() {
        document.getElementById('formPergunta').reset();
        document.getElementById('titulo-form').innerText = "Adicionar Nova Pergunta";
        document.getElementById('acao').value = "adicionar";
        document.getElementById('btn-cancelar').style.display = 'none';
        mostrarOpcoes();
    }

    function excluir(id) {
        if(!confirm('Deseja mesmo excluir?')) return;
        const fd = new FormData();
        fd.append('acao', 'excluir'); fd.append('id', id);
        
        fetch('AV1JSperguntasADM.php', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                document.getElementById('linha-' + id).remove();
            }
        });
    }
</script>
</body>
</html>