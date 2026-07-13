<?php
/* AV2 file: Endpoint que verifica se o usuário está logado no momento. */
if (isset($_SESSION['user_id'])) {
    echo json_encode(['logado' => true, 'nome' => $_SESSION['user_nome'], 'is_admin' => (bool)($_SESSION['is_admin'] ?? false)]);
} else {
    echo json_encode(['logado' => false]);
}
?>