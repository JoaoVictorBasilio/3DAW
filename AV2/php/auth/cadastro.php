<?php
/* AV2 file: Endpoint que trata solicitações de cadastro de novo usuário. */
/**
 * @var array $data
 * @var PDO $pdo
 */
if (empty(trim($data['nome'])) || empty(trim($data['email'])) || empty(trim($data['senha']))) {
    echo json_encode(['sucesso' => false, 'erro' => 'Todos os campos são obrigatórios.']); 
    exit;
}
$hash = password_hash(trim($data['senha']), PASSWORD_BCRYPT);
$stmt = $pdo->prepare("INSERT INTO av2_usuarios (nome, email, senha) VALUES (?,?,?)");
try {
    $stmt->execute([trim($data['nome']), trim($data['email']), $hash]);
    echo json_encode(['sucesso' => true]);
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'E-mail já existe']);
}
?>