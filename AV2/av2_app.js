// === DARK MODE INICIALIZAÇÃO === //
// Ao abrir o site, verifica se o usuário já tinha escolhido o tema escuro na última visita
if (localStorage.getItem('tema_almeidas') === 'dark') {
    document.body.classList.add('dark-theme');
}

// Alterna entre o tema claro e escuro e salva a preferência na memória do navegador
function toggleDarkMode() {
    document.body.classList.toggle('dark-theme');
    if (document.body.classList.contains('dark-theme')) {
        localStorage.setItem('tema_almeidas', 'dark');
    } else {
        localStorage.setItem('tema_almeidas', 'light');
    }
}

// === NOTIFICAÇÕES TOAST === //
// Cria e exibe os avisos coloridos flutuantes (sucesso, erro, info) e apaga após 3.5 segundos
function mostrarToast(mensagem, tipo = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${tipo}`;
    toast.innerText = mensagem;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('fadeOut');
        toast.addEventListener('animationend', () => toast.remove());
    }, 3500);
}

// === RESTANTE DO CÓDIGO === //
// Variáveis globais para guardar o status do usuário e dados da reserva durante a navegação
let usuarioLogado = false;
let is_admin = false;
let nomeHospedeGlobal = "";
let precoDiariaSelecionado = 0;
let listaDeQuartosGlobal = [];

// Inicia checando se o usuário já está logado
verificarSessao();

// Função principal de navegação: esconde todas as telas e mostra apenas a que foi clicada
function mudarTela(id) {
    document.querySelectorAll('.tela').forEach(t => t.classList.remove('ativa'));
    document.getElementById(id).classList.add('ativa');
}

// Pergunta ao PHP se existe uma sessão ativa para evitar que o usuário precise logar de novo
async function verificarSessao() {
    const res = await fetch('av2_api.php?action=verificar_sessao');
    const data = await res.json();
    if (data.logado) {
        usuarioLogado = true;
        is_admin = data.is_admin;
        nomeHospedeGlobal = data.nome;
        atualizarMenuSuperior(data.nome);
    }
}

// Controla o menu no topo: mostra/esconde botões dependendo se é visitante, cliente ou administrador
function atualizarMenuSuperior(nomeUsuario) {
    if (usuarioLogado) {
        document.getElementById('nav-login-btn').style.display = "none";
        document.getElementById('nav-user').innerText = "Olá, " + nomeUsuario;
        document.getElementById('nav-user').style.display = "inline-block";
        document.getElementById('nav-perfil').style.display = "inline-block";
        document.getElementById('nav-logout').style.display = "inline-block";
        document.getElementById('nav-admin').style.display = is_admin ? "inline-block" : "none";
    } else {
        document.getElementById('nav-login-btn').style.display = "inline-block";
        document.getElementById('nav-user').style.display = "none";
        document.getElementById('nav-perfil').style.display = "none";
        document.getElementById('nav-logout').style.display = "none";
        document.getElementById('nav-admin').style.display = "none";
    }
}

// === CONTAS E PERFIL === //

// Pega email e senha digitados e envia para validação no back-end
async function fazerLogin() {
    const email = document.getElementById('log_email').value.trim();
    const senha = document.getElementById('log_senha').value.trim();
    if (!email || !senha) return;

    const res = await fetch('av2_api.php?action=login', { method: 'POST', body: JSON.stringify({ email, senha }) });
    const data = await res.json();
    if (data.sucesso) {
        usuarioLogado = true; is_admin = data.is_admin; nomeHospedeGlobal = data.nome;
        atualizarMenuSuperior(data.nome);
        document.getElementById('log_email').value = ''; document.getElementById('log_senha').value = '';
        mostrarToast(`Bem-vindo de volta, ${data.nome}!`, 'sucesso');
        mudarTela('home');
    } else { 
        mostrarToast(data.erro, 'erro'); 
    }
}

// Envia os dados do novo cliente para salvar no banco de dados
async function fazerCadastro() {
    const nome = document.getElementById('cad_nome').value; const email = document.getElementById('cad_email').value; const senha = document.getElementById('cad_senha').value;
    if (!nome || !email || !senha) { mostrarToast("Preencha todos os campos do cadastro!", "erro"); return; }
    
    const res = await fetch('av2_api.php?action=cadastro', { method: 'POST', body: JSON.stringify({ nome, email, senha }) });
    const data = await res.json();
    if (data.sucesso) { 
        mostrarToast('Conta criada com sucesso! Faça login.', 'sucesso'); 
        mudarTela('login'); 
    } else { 
        mostrarToast(data.erro, 'erro'); 
    }
}

// Pede ao PHP para destruir a sessão e limpa a tela do usuário
async function fazerLogout() {
    await fetch('av2_api.php?action=logout');
    usuarioLogado = false; is_admin = false; atualizarMenuSuperior(''); mudarTela('home'); 
    mostrarToast("Você saiu da conta.", 'info');
}

// Busca os dados do cliente no banco e preenche os campos da tela "Meu Perfil"
async function abrirPerfil() {
    if (!usuarioLogado) { mostrarToast("Faça login primeiro!", 'erro'); mudarTela('login'); return; }
    const res = await fetch('av2_api.php?action=carregar_perfil');
    const data = await res.json();
    
    if(data.erro) { mostrarToast(data.erro, 'erro'); return; }
    
    document.getElementById('perfil_nome').value = data.nome;
    document.getElementById('perfil_email').value = data.email;
    document.getElementById('perfil_senha').value = ''; 
    
    mudarTela('perfil');
}

// Envia as alterações de nome/email/senha feitas pelo cliente para o PHP
async function salvarPerfil() {
    const nome = document.getElementById('perfil_nome').value.trim();
    const email = document.getElementById('perfil_email').value.trim();
    const senha = document.getElementById('perfil_senha').value.trim();
    
    if(!nome || !email) { mostrarToast("Nome e e-mail são obrigatórios!", 'erro'); return; }
    
    const res = await fetch('av2_api.php?action=editar_perfil', {
        method: 'POST', body: JSON.stringify({ nome, email, senha })
    });
    const data = await res.json();
    
    if(data.sucesso) {
        mostrarToast("Perfil atualizado com sucesso!", 'sucesso');
        nomeHospedeGlobal = data.nome;
        atualizarMenuSuperior(data.nome);
    } else {
        mostrarToast("Erro: " + data.erro, 'erro');
    }
}

// Pede confirmação e envia ordem para o PHP excluir a conta e todos os dados vinculados
async function apagarConta() {
    if(confirm("ATENÇÃO: Tem certeza que deseja apagar sua conta permanentemente? Todas as suas reservas e avaliações serão excluídas e não poderão ser recuperadas.")) {
        const res = await fetch('av2_api.php?action=apagar_conta');
        const data = await res.json();
        
        if(data.sucesso) {
            mostrarToast("Sua conta foi apagada com sucesso.", 'sucesso');
            usuarioLogado = false;
            is_admin = false;
            atualizarMenuSuperior('');
            mudarTela('home');
        } else {
            mostrarToast("Erro ao apagar: " + data.erro, 'erro');
        }
    }
}

// === FILTROS E QUARTOS === //

// Puxa do banco todos os quartos cadastrados e salva na lista global
async function carregarQuartos() {
    const res = await fetch('av2_api.php?action=quartos');
    listaDeQuartosGlobal = await res.json();
    renderizarQuartos(listaDeQuartosGlobal);
}

// Aplica a lógica da barra de pesquisa: filtra a lista global por preço e número de pessoas
function filtrarQuartos() {
    const maxPreco = parseFloat(document.getElementById('filtro_preco').value) || Infinity;
    const minPessoas = parseInt(document.getElementById('filtro_pessoas').value) || 0;
    
    const quartosFiltrados = listaDeQuartosGlobal.filter(q => q.preco <= maxPreco && q.pessoas >= minPessoas);
    renderizarQuartos(quartosFiltrados);
}

// Pega uma lista de quartos e injeta o código HTML de cada um deles na tela principal
function renderizarQuartos(quartos) {
    const div = document.getElementById('lista-quartos');
    div.innerHTML = '';
    quartos.forEach(q => {
        let notaMedia = parseFloat(q.media_notas).toFixed(1);
        let estrelas = notaMedia > 0 ? `⭐ ${notaMedia}` : `⭐ Novo`;
        
        div.innerHTML += `
            <div class="card-quarto">
                <img src="${q.imagem_url}" alt="Quarto">
                <div class="card-conteudo">
                    <h3>${q.nome}</h3>
                    <p class="specs">• ${q.banheiros} banheiro(s)</p>
                    <p class="specs">• ${q.camas} camas</p>
                    <p class="specs">• ${q.pessoas} pessoas</p>
                    <p style="color: #f59e0b; font-size: 0.85em; margin-top: 10px;">${estrelas}</p>
                    
                    <div class="card-footer">
                        <h4>R$ ${q.preco}</h4>
                        <button class="btn-roxo" style="padding: 8px 15px; font-size: 0.9em;" onclick="abrirPagamento(${q.id}, ${q.preco})">RESERVAR</button>
                    </div>
                </div>
            </div>
        `;
    });
}

// === PAGAMENTO E CALCULO === //

// Prepara a tela de pagamento guardando o ID e o Preço do quarto selecionado
function abrirPagamento(quarto_id, preco) {
    if (!usuarioLogado) { mostrarToast("Você precisa fazer login para reservar!", 'erro'); mudarTela('login'); return; }
    document.getElementById('pag_quarto_id').value = quarto_id;
    precoDiariaSelecionado = parseFloat(preco);
    
    document.getElementById('pag_checkin').value = '';
    document.getElementById('pag_checkout').value = '';
    document.getElementById('pag_valor_total').innerText = 'R$ 0.00';
    
    mudarFormaPagamento();
    mudarTela('pagamento');
}

// Disparado sempre que o usuário altera a data: calcula o número de dias e atualiza o valor total na tela
function calcularTotal() {
    const checkin = new Date(document.getElementById('pag_checkin').value);
    const checkout = new Date(document.getElementById('pag_checkout').value);
    
    if(checkin && checkout && checkout > checkin) {
        const diffTime = Math.abs(checkout - checkin);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        const total = diffDays * precoDiariaSelecionado;
        document.getElementById('pag_valor_total').innerText = 'R$ ' + total.toFixed(2);
    } else {
        document.getElementById('pag_valor_total').innerText = 'R$ 0.00';
    }
}

// Mostra o campo de Cartão ou o campo do Pix dependendo do que o usuário selecionar
function mudarFormaPagamento() {
    const metodo = document.getElementById('pag_metodo').value;
    document.getElementById('bloco_cartao').style.display = metodo === 'pix' ? 'none' : 'block';
    document.getElementById('bloco_pix').style.display = metodo === 'pix' ? 'block' : 'none';
}

// Valida os dados de checkin/checkout e envia o pedido final de reserva ao PHP
async function finalizarReserva() {
    const quarto_id = document.getElementById('pag_quarto_id').value;
    const data_inicio = document.getElementById('pag_checkin').value;
    const data_fim = document.getElementById('pag_checkout').value;
    const cpf = document.getElementById('pag_cpf').value;

    if(!data_inicio || !data_fim || !cpf) { mostrarToast("Preencha as datas e o CPF!", 'erro'); return; }

    const res = await fetch('av2_api.php?action=reservar', { method: 'POST', body: JSON.stringify({ quarto_id, data_inicio, data_fim, cpf }) });
    const data = await res.json();
    if (data.sucesso) { 
        mostrarToast("Reserva confirmada com sucesso!", 'sucesso'); 
        mudarTela('reservas'); 
        carregarReservas(); 
    } else { 
        mostrarToast(data.erro, 'erro'); 
    }
}

// === RESERVAS, VOUCHER E AVALIAÇÃO === //

// Busca no banco apenas as reservas confirmadas do hóspede logado e desenha na tela
async function carregarReservas() {
    if(!usuarioLogado) return;
    const res = await fetch('av2_api.php?action=minhas_reservas');
    const reservas = await res.json();
    const div = document.getElementById('lista-reservas');
    div.innerHTML = '';

    reservas.forEach(r => {
        div.innerHTML += `
            <div class="reserva-item">
                <div class="reserva-info">
                    <h3>${r.nome}</h3>
                    <p>Período: <strong class="datas">${r.data_inicio} até ${r.data_fim}</strong></p>
                    <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                        <button class="btn-azul" style="font-size: 0.85em; padding: 8px 15px;" onclick="imprimirVoucher('${r.nome}', '${r.data_inicio}', '${r.data_fim}')">🖨️ Imprimir Voucher</button>
                        <button class="btn-roxo" style="font-size: 0.85em; padding: 8px 15px; background:#f59e0b;" onclick="abrirAvaliacao(${r.quarto_id})">⭐ Avaliar Quarto</button>
                    </div>
                </div>
                <div style="display: flex; align-items: center; justify-content: flex-end; padding-left: 20px;">
                    <button class="btn-vermelho" onclick="cancelarReserva(${r.id})">CANCELAR</button>
                </div>
            </div>
        `;
    });
}

// Abre uma aba em branco e desenha um comprovante em HTML puro, acionando a impressora do usuário
function imprimirVoucher(quartoNome, inicio, fim) {
    let janela = window.open('', '', 'width=600,height=500');
    janela.document.write(`
        <html><head><title>Voucher - Almeida's Retreat</title></head>
        <body style="font-family: Arial, sans-serif; padding: 40px; text-align:center;">
            <h1 style="color: #8a2be2; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Almeida's Retreat</h1>
            <h2>Voucher Oficial de Hospedagem</h2>
            <div style="text-align: left; background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 10px; margin-top:20px;">
                <p><strong>Nome do Hóspede:</strong> ${nomeHospedeGlobal}</p>
                <p><strong>Quarto Reservado:</strong> ${quartoNome}</p>
                <p><strong>Data de Entrada (Check-in):</strong> ${inicio} (A partir das 14h)</p>
                <p><strong>Data de Saída (Check-out):</strong> ${fim} (Até às 12h)</p>
            </div>
            <p style="margin-top: 30px; font-size: 0.9em; color:#555;">Apresente este documento na recepção do hotel.</p>
            <button onclick="window.print()" style="margin-top:20px; padding:10px 20px; background:#8a2be2; color:white; border:none; border-radius:5px; cursor:pointer;">Imprimir</button>
        </body></html>
    `);
    janela.document.close();
}

// Puxa o ID do quarto que o cliente clicou e abre a telinha modal de avaliação
function abrirAvaliacao(quarto_id) {
    document.getElementById('aval_quarto_id').value = quarto_id;
    mudarTela('avaliacao_tela');
}

// Coleta as estrelas dadas e a resenha escrita para enviar ao banco de dados
async function enviarAvaliacao() {
    const quarto_id = document.getElementById('aval_quarto_id').value;
    const nota = document.getElementById('aval_nota').value;
    const comentario = document.getElementById('aval_comentario').value;

    const res = await fetch('av2_api.php?action=avaliar_quarto', { method: 'POST', body: JSON.stringify({ quarto_id, nota, comentario }) });
    const data = await res.json();
    if(data.sucesso) { 
        mostrarToast("Obrigado pela sua avaliação!", 'sucesso'); 
        mudarTela('reservas'); 
        carregarQuartos(); 
    }
}

// Confirma a decisão e pede para o PHP mudar o status da reserva para "Cancelada"
async function cancelarReserva(reserva_id) {
    if(confirm("Cancelar reserva?")) {
        await fetch('av2_api.php?action=cancelar_reserva', { method: 'POST', body: JSON.stringify({ reserva_id }) });
        mostrarToast("Reserva cancelada.", 'info');
        carregarReservas();
    }
}

// === ADMIN E UPLOAD === //

// Agrupa a chamada de todas as funções do painel para carregar a tela completa de uma vez
async function abrirPainelAdmin() {
    mudarTela('painel_admin'); carregarAdminDashboard(); carregarAdminReservas(); carregarAdminQuartos();
}

// Função de upload: cria um "FormData" para conseguir enviar texto (nome, preço) E arquivo (foto do quarto) juntos
async function adminAddQuarto() {
    const nome = document.getElementById('admin_q_nome').value;
    const banheiros = document.getElementById('admin_q_banheiros').value;
    const camas = document.getElementById('admin_q_camas').value;
    const pessoas = document.getElementById('admin_q_pessoas').value;
    const preco = document.getElementById('admin_q_preco').value;
    const imgInput = document.getElementById('admin_q_img'); 

    if(!nome || !banheiros || !camas || !pessoas || !preco) { mostrarToast("Preencha os dados básicos do quarto!", 'erro'); return; }

    let formData = new FormData();
    formData.append('nome', nome);
    formData.append('banheiros', banheiros);
    formData.append('camas', camas);
    formData.append('pessoas', pessoas);
    formData.append('preco', preco);
    
    // Se o admin selecionou uma imagem, adiciona ao pacote
    if(imgInput.files.length > 0) { formData.append('imagem', imgInput.files[0]); }

    const res = await fetch('av2_api.php?action=admin_add_quarto', { method: 'POST', body: formData });
    const data = await res.json();
    if(data.sucesso) { 
        mostrarToast("Quarto adicionado com sucesso!", 'sucesso'); 
        carregarAdminQuartos(); 
    }
}

// Busca a soma de receita e o total de reservas ativas para os cards do topo do painel
async function carregarAdminDashboard() {
    const res = await fetch('av2_api.php?action=admin_dashboard'); const stats = await res.json();
    document.getElementById('admin_dashboard_cards').innerHTML = `
        <div class="card-quarto" style="text-align:center; background:#dcfce7;"><h3>Reservas Ativas</h3><h1 style="color:#22c55e; font-size:3em;">${stats.reservas_ativas}</h1></div>
        <div class="card-quarto" style="text-align:center; background:#fae8ff;"><h3>Receita Estimada</h3><h1 style="color:#c026d3; font-size:2.5em;">R$ ${stats.receita ? parseFloat(stats.receita).toFixed(2) : "0.00"}</h1></div>
    `;
}

// Lista o painel de contabilidade: mostra quem reservou, as datas e formata com cores dependendo do status
async function carregarAdminReservas() {
    const res = await fetch('av2_api.php?action=admin_reservas'); const reservas = await res.json();
    const div = document.getElementById('admin-lista-reservas'); div.innerHTML = '';
    if(reservas.erro) return;
    reservas.forEach(r => {
        let cor = r.status === 'Ativa' ? 'green' : (r.status === 'Manutencao' ? 'orange' : 'red');
        let txtCliente = r.status === 'Manutencao' ? 'BLOQUEIO' : `${r.cliente}`;
        div.innerHTML += `
            <div class="reserva-item" style="border-left: 5px solid ${cor}">
                <div><h3>${r.quarto}</h3><p>${txtCliente} | ${r.data_inicio} até ${r.data_fim}</p></div>
                <div style="text-align:right;"><h3 style="color:${cor};">${r.status}</h3></div>
            </div>`;
    });
}

// Lista os quartos na parte de baixo do painel e alimenta o `<select>` (caixinha de opções) do bloqueio de datas
async function carregarAdminQuartos() {
    const res = await fetch('av2_api.php?action=quartos'); const quartos = await res.json();
    const div = document.getElementById('admin-lista-quartos'); div.innerHTML = '';
    const select = document.getElementById('admin_b_quarto'); select.innerHTML = '';
    quartos.forEach(q => {
        div.innerHTML += `<div class="card-quarto" style="text-align:center;"><h3>${q.nome}</h3><p>R$ ${q.preco}</p><button class="btn-vermelho" style="margin-top:10px;" onclick="adminDelQuarto(${q.id})">Remover</button></div>`;
        select.innerHTML += `<option value="${q.id}">${q.nome}</option>`;
    });
}

// Confirmação para evitar exclusão acidental e chamada ao PHP para apagar quarto
async function adminDelQuarto(quarto_id) { 
    if(confirm("Apagar quarto?")) { 
        const res = await fetch('av2_api.php?action=admin_del_quarto', { method: 'POST', body: JSON.stringify({ quarto_id }) }); 
        const data = await res.json();
        if(data.sucesso) {
            mostrarToast("Quarto removido com sucesso.", 'sucesso');
            carregarAdminQuartos(); 
        } else {
            mostrarToast("Erro: " + data.erro, 'erro');
        }
    } 
}

// Bloqueia um quarto para manutenção e manda o PHP resolver os conflitos de clientes agendados
async function adminBloquearDatas() {
    const quarto_id = document.getElementById('admin_b_quarto').value; const data_inicio = document.getElementById('admin_b_inicio').value; const data_fim = document.getElementById('admin_b_fim').value;
    if(!quarto_id || !data_inicio || !data_fim) return;
    const res = await fetch('av2_api.php?action=admin_bloquear_datas', { method: 'POST', body: JSON.stringify({ quarto_id, data_inicio, data_fim }) });
    const data = await res.json();
    if(data.sucesso) {
        mostrarToast("Datas bloqueadas e conflitos resolvidos!", 'sucesso'); 
        carregarAdminReservas();
    } else {
        mostrarToast("Erro: " + data.erro, 'erro');
    }
}