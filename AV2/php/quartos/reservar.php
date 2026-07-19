<?php
/* AV2 file: Endpoint que cria uma nova reserva de quarto. */
/**
 * @var array $data
 * @var PDO $pdo
 */
if (!isset($_SESSION['user_id'])) { echo json_encode(['erro' => 'Não autenticado']); exit; }

$stmt = $pdo->prepare("SELECT COUNT(*) as conf FROM av2_reservas WHERE quarto_id = ? AND status IN ('Ativa', 'Manutencao') AND data_inicio < ? AND data_fim > ?");
$stmt->execute([$data['quarto_id'], $data['data_fim'], $data['data_inicio']]);
if ($stmt->fetch()['conf'] > 0) { echo json_encode(['sucesso' => false, 'erro' => 'Quarto indisponível nestas datas.']); exit; }

$stmt = $pdo->prepare("INSERT INTO av2_reservas (user_id, quarto_id, data_inicio, data_fim, cpf) VALUES (?,?,?,?,?)");
$stmt->execute([$_SESSION['user_id'], $data['quarto_id'], $data['data_inicio'], $data['data_fim'], $data['cpf']]);
echo json_encode(['sucesso' => true]);
?>