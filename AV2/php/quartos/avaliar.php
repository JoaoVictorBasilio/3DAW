<?php
/* AV2 file: Endpoint que salva avaliações de quartos feitas por hóspedes. */
/**
 * @var array $data
 * @var PDO $pdo
 */
if (!isset($_SESSION['user_id'])) { echo json_encode(['erro' => 'Não autenticado']); exit; }
$stmt = $pdo->prepare("INSERT INTO av2_avaliacoes (user_id, quarto_id, nota, comentario) VALUES (?,?,?,?)");
$stmt->execute([$_SESSION['user_id'], $data['quarto_id'], $data['nota'], $data['comentario']]);
echo json_encode(['sucesso' => true]);
?>