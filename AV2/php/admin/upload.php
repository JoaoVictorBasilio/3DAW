<?php
/* AV2 file: Endpoint admin para adicionar um novo quarto, incluindo upload de imagem. */
/**
 * @var array $data
 * @var PDO $pdo
 */
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) { echo json_encode(['erro' => 'Negado']); exit; }

$imagem_url = 'placeholder.jpg';

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
    $pasta_destino = 'uploads/';
    if (!is_dir($pasta_destino)) { mkdir($pasta_destino, 0777, true); }
    
    $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $nome_arquivo = uniqid() . '.' . $extensao;
    
    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $pasta_destino . $nome_arquivo)) {
        $imagem_url = $pasta_destino . $nome_arquivo;
    }
}

$stmt = $pdo->prepare("INSERT INTO av2_quartos (nome, banheiros, camas, pessoas, preco, imagem_url) VALUES (?,?,?,?,?,?)");
$stmt->execute([$data['nome'], $data['banheiros'], $data['camas'], $data['pessoas'], $data['preco'], $imagem_url]);
echo json_encode(['sucesso' => true]);
?>