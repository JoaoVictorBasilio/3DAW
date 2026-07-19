<?php
/* AV2 file: Endpoint admin que retorna métricas do painel. */
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) { echo json_encode(['erro' => 'Negado']); exit; }
$metricas['reservas_ativas'] = $pdo->query("SELECT COUNT(*) FROM av2_reservas WHERE status = 'Ativa'")->fetchColumn();
$metricas['receita'] = $pdo->query("SELECT SUM(q.preco * DATEDIFF(r.data_fim, r.data_inicio)) FROM av2_reservas r JOIN av2_quartos q ON r.quarto_id = q.id WHERE r.status = 'Ativa'")->fetchColumn();
echo json_encode($metricas);
?>