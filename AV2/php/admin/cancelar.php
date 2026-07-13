<?php
/* AV2 file: Endpoint admin para cancelar uma reserva pelo painel. */
/**
 * @var array $data
 * @var PDO $pdo
 */
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) { echo json_encode(['erro' => 'Negado']); exit; }
$pdo->prepare("UPDATE av2_reservas SET status = 'Cancelada' WHERE id = ?")->execute([$data['reserva_id']]);
echo json_encode(['sucesso' => true]);
?>