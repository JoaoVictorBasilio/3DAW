<?php
/* AV2 file: Endpoint para excluir a conta do usuário autenticado. */
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
?>