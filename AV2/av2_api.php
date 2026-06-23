<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'almeidas_retreat';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro de conexão com o banco']);
    exit;
}

$action = $_GET['action']?? '';
$data = json_decode(file_get_contents('php://input'), true);

switch ($action) {
    case 'cadastro':
        $hash = password_hash($data['senha'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO av2_usuarios (nome, email, senha) VALUES (?,?,?)");
        try {
            $stmt->execute([$data['nome'], $data['email'], $hash]);
            echo json_encode(['sucesso' => true]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => 'E-mail já existe']);
        }
        break;

    case 'login':
        $stmt = $pdo->prepare("SELECT id, nome, senha FROM av2_usuarios WHERE email =?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($data['senha'], $user['senha'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            echo json_encode(['sucesso' => true, 'nome' => $user['nome']]);
        } else {
            echo json_encode(['sucesso' => false, 'erro' => 'Credenciais inválidas']);
        }
        break;

    case 'logout':
        session_destroy();
        echo json_encode(['sucesso' => true]);
        break;

    case 'quartos':
        $stmt = $pdo->query("SELECT * FROM av2_quartos");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'reservar':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['sucesso' => false, 'erro' => 'Não autenticado']);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO av2_reservas (user_id, quarto_id, data_inicio, data_fim, cpf) VALUES (?,?,?,?,?)");
        $stmt->execute(, $data['quarto_id'], $data['data_inicio'], $data['data_fim'], $data['cpf']]);
        echo json_encode(['sucesso' => true]);
        break;

    case 'minhas_reservas':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode();
            exit;
        }
        $stmt = $pdo->prepare("
            SELECT r.id, r.data_inicio, r.data_fim, q.nome, q.banheiros, q.camas, q.pessoas 
            FROM av2_reservas r 
            JOIN av2_quartos q ON r.quarto_id = q.id 
            WHERE r.user_id =? AND r.status = 'Ativa'
        ");
        $stmt->execute(]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'cancelar_reserva':
        $stmt = $pdo->prepare("UPDATE av2_reservas SET status = 'Cancelada' WHERE id =? AND user_id =?");
        $stmt->execute([$data['reserva_id'], $_SESSION['user_id']]);
        echo json_encode(['sucesso' => true]);
        break;
}
?>
