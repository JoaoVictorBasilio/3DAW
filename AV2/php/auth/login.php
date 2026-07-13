<?php
/* AV2 file: Endpoint que verifica credenciais do usuário e inicia a sessão. */
/**
 * @var array $data
 * @var PDO $pdo
 */
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
?>