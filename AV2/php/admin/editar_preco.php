<?php
/* AV2 file: Endpoint admin para atualizar o preço do quarto. */
/**
 * @var array $data
 * @var PDO $pdo
 */
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) { echo json_encode(['erro' => 'Negado']); exit; }
$pdo->prepare("UPDATE av2_quartos SET preco = ? WHERE id = ?")->execute([$data['novo_preco'], $data['quarto_id']]);
echo json_encode(['sucesso' => true]);
?>