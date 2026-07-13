<?php
/* AV2 file: Endpoint para carregar dados de perfil do usuário atual. */
if (!isset($_SESSION['user_id'])) { echo json_encode(['erro' => 'Não autenticado']); exit; }
$stmt = $pdo->prepare("SELECT nome, email FROM av2_usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
?>