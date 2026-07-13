<?php
/* AV2 file: Endpoint admin que retorna a lista de reservas atuais. */
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) { echo json_encode(['erro' => 'Negado']); exit; }
$stmt = $pdo->query("SELECT r.id, r.data_inicio, r.data_fim, r.status, q.nome as quarto, u.nome as cliente, u.email FROM av2_reservas r JOIN av2_quartos q ON r.quarto_id = q.id LEFT JOIN av2_usuarios u ON r.user_id = u.id ORDER BY r.data_inicio DESC");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>