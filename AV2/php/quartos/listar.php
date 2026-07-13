<?php
/* AV2 file: Endpoint que retorna a lista de quartos disponíveis. */
$stmt = $pdo->query("
    SELECT q.*, 
           COALESCE(AVG(a.nota), 0) as media_notas, 
           COUNT(a.id) as total_avaliacoes 
    FROM av2_quartos q 
    LEFT JOIN av2_avaliacoes a ON q.id = a.quarto_id 
    GROUP BY q.id
");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>