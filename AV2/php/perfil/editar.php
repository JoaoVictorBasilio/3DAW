<?php
/* AV2 file: Endpoint para atualizar informações de perfil do usuário autenticado. */
/**
 * @var array $data
 * @var PDO $pdo
 */
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
    $_SESSION['user_nome'] = $nome;
    echo json_encode(['sucesso' => true, 'nome' => $nome]);
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Este e-mail já está em uso por outra conta.']);
}
?>