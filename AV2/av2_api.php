<?php
// Inicia a sessão (para manter o login) e avisa o navegador que a resposta será em formato JSON
session_start();
header('Content-Type: application/json');

// Configurações de conexão com o banco de dados
$host = 'localhost';
$dbname = 'almeidas_retreat';
$user = 'root';
$pass = '';

// Tenta conectar ao banco de dados; se falhar, devolve um erro e para o código
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro de conexão com o banco']);
    exit;
}

// Pega a ação que o Front-end quer executar (ex: action=login, action=quartos)
$action = $_GET['action'] ?? '';

// Lê os dados que vieram do Front-end (aceita tanto JSON padrão quanto FormData para upload de imagens)
$data = json_decode(file_get_contents('php://input'), true);
if (!$data && !empty($_POST)) {
    $data = $_POST; 
}

// Analisa qual ação foi pedida e executa o bloco de código correspondente
switch ($action) {
    
    // Serve para verificar se o usuário já entrou na conta quando ele atualiza a página
    case 'verificar_sessao':
        if (isset($_SESSION['user_id'])) {
            echo json_encode(['logado' => true, 'nome' => $_SESSION['user_nome'], 'is_admin' => (bool)($_SESSION['is_admin'] ?? false)]);
        } else {
            echo json_encode(['logado' => false]);
        }
        break;

    // Rota para cadastrar novos usuários (clientes) no sistema, criptografando a senha
    case 'cadastro':
        if (empty(trim($data['nome'])) || empty(trim($data['email'])) || empty(trim($data['senha']))) {
            echo json_encode(['sucesso' => false, 'erro' => 'Todos os campos são obrigatórios.']); exit;
        }
        $hash = password_hash(trim($data['senha']), PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO av2_usuarios (nome, email, senha) VALUES (?,?,?)");
        try {
            $stmt->execute([trim($data['nome']), trim($data['email']), $hash]);
            echo json_encode(['sucesso' => true]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => 'E-mail já existe']);
        }
        break;

    // Rota para validar email e senha e iniciar a sessão do usuário
    case 'login':
        $stmt = $pdo->prepare("SELECT id, nome, senha, is_admin FROM av2_usuarios WHERE email = ?");
        $stmt->execute([trim($data['email'])]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify(trim($data['senha']), $user['senha'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['is_admin'] = $user['is_admin'];
            echo json_encode(['sucesso' => true, 'nome' => $user['nome'], 'is_admin' => (bool)$user['is_admin']]);
        } else {
            echo json_encode(['sucesso' => false, 'erro' => 'Credenciais inválidas']);
        }
        break;

    // Serve para deslogar o usuário e destruir a sessão salva no servidor
    case 'logout':
        session_destroy();
        echo json_encode(['sucesso' => true]);
        break;

    // ================= ROTAS DO PERFIL ================= //
    
    // Busca os dados do usuário logado para preencher a tela de "Meu Perfil"
    case 'carregar_perfil':
        if (!isset($_SESSION['user_id'])) { echo json_encode(['erro' => 'Não autenticado']); exit; }
        $stmt = $pdo->prepare("SELECT nome, email FROM av2_usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        break;

    // Atualiza os dados (nome, email, senha) caso o cliente altere no perfil
    case 'editar_perfil':
        if (!isset($_SESSION['user_id'])) { echo json_encode(['erro' => 'Não autenticado']); exit; }
        
        $nome = trim($data['nome']);
        $email = trim($data['email']);
        $senha = trim($data['senha']);

        if (empty($nome) || empty($email)) {
            echo json_encode(['sucesso' => false, 'erro' => 'Nome e e-mail são obrigatórios.']); exit;
        }

        try {
            if (!empty($senha)) {
                $hash = password_hash($senha, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE av2_usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?");
                $stmt->execute([$nome, $email, $hash, $_SESSION['user_id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE av2_usuarios SET nome = ?, email = ? WHERE id = ?");
                $stmt->execute([$nome, $email, $_SESSION['user_id']]);
            }
            $_SESSION['user_nome'] = $nome; // Atualiza a sessão
            echo json_encode(['sucesso' => true, 'nome' => $nome]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => 'Este e-mail já está em uso por outra conta.']);
        }
        break;

    // Exclui a conta do cliente permanentemente (apagando avaliações e reservas antes para evitar erro no banco)
    case 'apagar_conta':
        if (!isset($_SESSION['user_id'])) { echo json_encode(['erro' => 'Não autenticado']); exit; }
        try {
            $pdo->prepare("DELETE FROM av2_avaliacoes WHERE user_id = ?")->execute([$_SESSION['user_id']]);
            $pdo->prepare("DELETE FROM av2_reservas WHERE user_id = ?")->execute([$_SESSION['user_id']]);
            $pdo->prepare("DELETE FROM av2_usuarios WHERE id = ?")->execute([$_SESSION['user_id']]);
            
            session_destroy();
            echo json_encode(['sucesso' => true]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => 'Ocorreu um erro ao apagar a conta.']);
        }
        break;

    // ================= ROTAS DE QUARTOS E RESERVAS ================= //
    
    // Lista todos os quartos na página principal, calculando a média das notas (estrelinhas) de cada um
    case 'quartos':
        $stmt = $pdo->query("
            SELECT q.*, 
                   COALESCE(AVG(a.nota), 0) as media_notas, 
                   COUNT(a.id) as total_avaliacoes 
            FROM av2_quartos q 
            LEFT JOIN av2_avaliacoes a ON q.id = a.quarto_id 
            GROUP BY q.id
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    // Processa uma nova reserva: primeiro checa se há conflito de datas, se estiver livre, salva no banco
    case 'reservar':
        if (!isset($_SESSION['user_id'])) { echo json_encode(['erro' => 'Não autenticado']); exit; }
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as conf FROM av2_reservas WHERE quarto_id = ? AND status IN ('Ativa', 'Manutencao') AND data_inicio < ? AND data_fim > ?");
        $stmt->execute([$data['quarto_id'], $data['data_fim'], $data['data_inicio']]);
        if ($stmt->fetch()['conf'] > 0) { echo json_encode(['sucesso' => false, 'erro' => 'Quarto indisponível nestas datas.']); exit; }

        $stmt = $pdo->prepare("INSERT INTO av2_reservas (user_id, quarto_id, data_inicio, data_fim, cpf) VALUES (?,?,?,?,?)");
        $stmt->execute([$_SESSION['user_id'], $data['quarto_id'], $data['data_inicio'], $data['data_fim'], $data['cpf']]);
        echo json_encode(['sucesso' => true]);
        break;

    // Busca apenas as reservas "Ativas" do usuário que está logado no momento
    case 'minhas_reservas':
        if (!isset($_SESSION['user_id'])) { echo json_encode(['erro' => 'Não autenticado']); exit; }
        $stmt = $pdo->prepare("SELECT r.id, r.quarto_id, r.data_inicio, r.data_fim, q.nome, q.banheiros, q.camas, q.pessoas FROM av2_reservas r JOIN av2_quartos q ON r.quarto_id = q.id WHERE r.user_id = ? AND r.status = 'Ativa'");
        $stmt->execute([$_SESSION['user_id']]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    // Altera o status da reserva do cliente de 'Ativa' para 'Cancelada'
    case 'cancelar_reserva':
        $stmt = $pdo->prepare("UPDATE av2_reservas SET status = 'Cancelada' WHERE id = ? AND user_id = ?");
        $stmt->execute([$data['reserva_id'], $_SESSION['user_id']]);
        echo json_encode(['sucesso' => true]);
        break;

    // Salva a nota e o comentário que o hóspede deu para um quarto após a estadia
    case 'avaliar_quarto':
        if (!isset($_SESSION['user_id'])) { echo json_encode(['erro' => 'Não autenticado']); exit; }
        $stmt = $pdo->prepare("INSERT INTO av2_avaliacoes (user_id, quarto_id, nota, comentario) VALUES (?,?,?,?)");
        $stmt->execute([$_SESSION['user_id'], $data['quarto_id'], $data['nota'], $data['comentario']]);
        echo json_encode(['sucesso' => true]);
        break;

    // ================= ROTAS DO ADMIN ================= //
    
    // (Apenas Admin) Coleta os números totais para mostrar nos cards verdes e roxos do Painel (Visão Geral)
    case 'admin_dashboard':
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) { echo json_encode(['erro' => 'Negado']); exit; }
        $metricas['reservas_ativas'] = $pdo->query("SELECT COUNT(*) FROM av2_reservas WHERE status = 'Ativa'")->fetchColumn();
        $metricas['receita'] = $pdo->query("SELECT SUM(q.preco * DATEDIFF(r.data_fim, r.data_inicio)) FROM av2_reservas r JOIN av2_quartos q ON r.quarto_id = q.id WHERE r.status = 'Ativa'")->fetchColumn();
        echo json_encode($metricas);
        break;

    // (Apenas Admin) Lista todas as reservas feitas no hotel, incluindo o nome e email de cada cliente
    case 'admin_reservas':
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) { echo json_encode(['erro' => 'Negado']); exit; }
        $stmt = $pdo->query("SELECT r.id, r.data_inicio, r.data_fim, r.status, q.nome as quarto, u.nome as cliente, u.email FROM av2_reservas r JOIN av2_quartos q ON r.quarto_id = q.id LEFT JOIN av2_usuarios u ON r.user_id = u.id ORDER BY r.data_inicio DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    // (Apenas Admin) Cancela a reserva de qualquer pessoa pelo Painel Administrativo
    case 'admin_cancelar_reserva':
        $pdo->prepare("UPDATE av2_reservas SET status = 'Cancelada' WHERE id = ?")->execute([$data['reserva_id']]);
        echo json_encode(['sucesso' => true]);
        break;

    // (Apenas Admin) Bloqueia um quarto para manutenção. Se alguém tiver reserva nessas datas, o sistema tenta mover a pessoa para outro quarto igual, senão, cancela a reserva dela.
    case 'admin_bloquear_datas':
        $stmt = $pdo->prepare("SELECT id, data_inicio, data_fim FROM av2_reservas WHERE quarto_id = ? AND status = 'Ativa' AND data_inicio < ? AND data_fim > ?");
        $stmt->execute([$data['quarto_id'], $data['data_fim'], $data['data_inicio']]);
        $conflitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $q_orig = $pdo->prepare("SELECT banheiros, camas, pessoas FROM av2_quartos WHERE id = ?"); $q_orig->execute([$data['quarto_id']]); $qo = $q_orig->fetch();

        foreach ($conflitos as $c) {
            $busca = $pdo->prepare("SELECT id FROM av2_quartos WHERE id != ? AND banheiros = ? AND camas = ? AND pessoas = ? AND id NOT IN (SELECT quarto_id FROM av2_reservas WHERE status IN ('Ativa', 'Manutencao') AND data_inicio < ? AND data_fim > ?) LIMIT 1");
            $busca->execute([$data['quarto_id'], $qo['banheiros'], $qo['camas'], $qo['pessoas'], $c['data_fim'], $c['data_inicio']]);
            $novo_quarto = $busca->fetch();
            if ($novo_quarto) { $pdo->prepare("UPDATE av2_reservas SET quarto_id = ? WHERE id = ?")->execute([$novo_quarto['id'], $c['id']]); }
            else { $pdo->prepare("UPDATE av2_reservas SET status = 'Cancelada' WHERE id = ?")->execute([$c['id']]); }
        }
        $pdo->prepare("INSERT INTO av2_reservas (user_id, quarto_id, data_inicio, data_fim, status, cpf) VALUES (?,?,?,?, 'Manutencao', 'ADMIN')")->execute([$_SESSION['user_id'], $data['quarto_id'], $data['data_inicio'], $data['data_fim']]);
        echo json_encode(['sucesso' => true]);
        break;

    // (Apenas Admin) Cadastra um quarto novo. Também recebe a foto enviada, cria um nome único para ela e salva na pasta 'uploads/'
    case 'admin_add_quarto':
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) { echo json_encode(['erro' => 'Negado']); exit; }
        
        $imagem_url = 'placeholder.jpg';
        
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $pasta_destino = 'uploads/';
            if (!is_dir($pasta_destino)) { mkdir($pasta_destino, 0777, true); }
            
            $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $nome_arquivo = uniqid() . '.' . $extensao;
            
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $pasta_destino . $nome_arquivo)) {
                $imagem_url = $pasta_destino . $nome_arquivo;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO av2_quartos (nome, banheiros, camas, pessoas, preco, imagem_url) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$data['nome'], $data['banheiros'], $data['camas'], $data['pessoas'], $data['preco'], $imagem_url]);
        echo json_encode(['sucesso' => true]);
        break;
        
    // (Apenas Admin) Atualiza o preço da diária de um quarto existente
    case 'admin_editar_preco':
        $pdo->prepare("UPDATE av2_quartos SET preco = ? WHERE id = ?")->execute([$data['novo_preco'], $data['quarto_id']]);
        echo json_encode(['sucesso' => true]); break;

    // (Apenas Admin) Apaga um quarto do banco de dados (retorna erro se o quarto já tiver reservas atreladas a ele)
    case 'admin_del_quarto':
        try { $pdo->prepare("DELETE FROM av2_quartos WHERE id = ?")->execute([$data['quarto_id']]); echo json_encode(['sucesso' => true]); }
        catch (Exception $e) { echo json_encode(['sucesso' => false, 'erro' => 'Possui reservas vinculadas.']); }
        break;
}
?>