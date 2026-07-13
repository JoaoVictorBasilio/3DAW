<?php
/* AV2 file: Endpoint que retorna as reservas atuais do usuário. */
if (!isset($_SESSION['user_id'])) { echo json_encode(['erro' => 'Não autenticado']); exit; }
$stmt = $pdo->prepare("SELECT r.id, r.quarto_id, r.data_inicio, r.data_fim, q.nome, q.banheiros, q.camas, q.pessoas FROM av2_reservas r JOIN av2_quartos q ON r.quarto_id = q.id WHERE r.user_id = ? AND r.status = 'Ativa'");
$stmt->execute([$_SESSION['user_id']]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>