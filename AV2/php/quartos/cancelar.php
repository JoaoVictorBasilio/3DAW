<?php
/* AV2 file: Endpoint que cancela uma reserva do hóspede. */
/**
 * @var array $data
 * @var PDO $pdo
 */
if (!isset($_SESSION['user_id'])) { echo json_encode(['erro' => 'Não autenticado']); exit; }
$stmt = $pdo->prepare("UPDATE av2_reservas SET status = 'Cancelada' WHERE id = ? AND user_id = ?");
$stmt->execute([$data['reserva_id'], $_SESSION['user_id']]);
echo json_encode(['sucesso' => true]);
?>