<?php
/* AV2 file: Endpoint admin para bloquear datas e tratar conflitos de quartos. */
/**
 * @var array $data
 * @var PDO $pdo
 */
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) { echo json_encode(['erro' => 'Negado']); exit; }
$stmt = $pdo->prepare("SELECT id, data_inicio, data_fim FROM av2_reservas WHERE quarto_id = ? AND status = 'Ativa' AND data_inicio < ? AND data_fim > ?");
$stmt->execute([$data['quarto_id'], $data['data_fim'], $data['data_inicio']]);
$conflitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$q_orig = $pdo->prepare("SELECT banheiros, camas, pessoas FROM av2_quartos WHERE id = ?"); $q_orig->execute([$data['quarto_id']]); $qo = $q_orig->fetch();

foreach ($conflitos as $c) {
    $busca = $pdo->prepare("SELECT id FROM av2_quartos WHERE id != ? AND banheiros = ? AND camas = ? AND pessoas = ? AND id NOT IN (SELECT quarto_id FROM av2_reservas WHERE status IN ('Ativa', 'Manutencao') AND data_inicio < ? AND data_fim > ?) LIMIT 1");
    $busca->execute([$data['quarto_id'], $qo['banheiros'], $qo['camas'], $qo['pessoas'], $c['data_fim'], $c['data_inicio']]);
    $novo_quarto = $busca->fetch();
    if ($novo_quarto) { $pdo->prepare("UPDATE av2_reservas SET quarto_id = ? WHERE id = ?")->execute([$novo_quarto['id'], $c['id']]); }
    else { $pdo->prepare("UPDATE av2_reservas SET status = 'Cancelada' WHERE id = ?")->execute([$c['id']]); }
}
$pdo->prepare("INSERT INTO av2_reservas (user_id, quarto_id, data_inicio, data_fim, status, cpf) VALUES (?,?,?,?, 'Manutencao', 'ADMIN')")->execute([$_SESSION['user_id'], $data['quarto_id'], $data['data_inicio'], $data['data_fim']]);
echo json_encode(['sucesso' => true]);
?>