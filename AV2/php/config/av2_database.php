<?php
/* AV2 file: Configurações de conexão com banco de dados usadas no backend do AV2. */
$host = 'localhost';
$dbname = 'almeidas_retreat';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro de conexão com o banco']);
    exit;
}
?>