<?php
/* AV2 file: Endpoint que encerra a sessão do usuário e faz logout. */
session_destroy();
echo json_encode(['sucesso' => true]);
?>