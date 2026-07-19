<?php
/* AV2 file: Roteador principal da API que encaminha solicitações ao manipulador backend adequado. */
// Inicia a sessão e define o retorno como JSON
session_start();
header('Content-Type: application/json');

// Puxa a conexão com o banco de dados
require_once 'config/av2_database.php';

// Pega a ação requerida
$action = $_GET['action'] ?? '';

// Lê os dados recebidos via POST/Fetch
$data = json_decode(file_get_contents('php://input'), true);
if (!$data && !empty($_POST)) {
    $data = $_POST; 
}

// Roteador: Direciona para o arquivo correto baseado na ação
switch ($action) {
    // Auth
    case 'verificar_sessao': require 'auth/sessao.php'; break;
    case 'cadastro':         require 'auth/cadastro.php'; break;
    case 'login':            require 'auth/login.php'; break;
    case 'logout':           require 'auth/logout.php'; break;

    // Perfil
    case 'carregar_perfil':  require 'perfil/carregar.php'; break;
    case 'editar_perfil':    require 'perfil/editar.php'; break;
    case 'apagar_conta':     require 'perfil/apagar.php'; break;

    // Quartos e Reservas (Hóspedes)
    case 'quartos':          require 'quartos/listar.php'; break;
    case 'reservar':         require 'quartos/reservar.php'; break;
    case 'minhas_reservas':  require 'quartos/reservas.php'; break;
    case 'cancelar_reserva': require 'quartos/cancelar.php'; break;
    case 'avaliar_quarto':   require 'quartos/avaliar.php'; break;

    // Painel Admin
    case 'admin_dashboard':        require 'admin/dashboard.php'; break;
    case 'admin_reservas':         require 'admin/reservas.php'; break;
    case 'admin_cancelar_reserva': require 'admin/cancelar.php'; break;
    case 'admin_bloquear_datas':   require 'admin/bloquear.php'; break;
    case 'admin_add_quarto':       require 'admin/upload.php'; break;
    case 'admin_editar_preco':     require 'admin/editar_preco.php'; break;
    case 'admin_del_quarto':       require 'admin/apagar_quarto.php'; break;

    default:
        echo json_encode(['erro' => 'Ação não encontrada ou inválida.']);
        break;
}
?>