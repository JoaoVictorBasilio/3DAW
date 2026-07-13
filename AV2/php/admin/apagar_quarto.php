<?php
/* AV2 file: Endpoint admin para deletar um quarto quando permitido. */
/**
 * @var array $data
 * @var PDO $pdo
 */
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) { echo json_encode(['erro' => 'Negado']); exit; }
try { 
    $pdo->prepare("DELETE FROM av2_quartos WHERE id = ?")->execute([$data['quarto_id']]); 
    echo json_encode(['sucesso' => true]); 
} catch (Exception $e) { 
    echo json_encode(['sucesso' => false, 'erro' => 'Possui reservas vinculadas.']); 
}
?>