// AV2 file: Lógica do cliente admin para gerenciar quartos, reservas e ações do dashboard.
async function abrirPainelAdmin() {
    mudarTela('painel_admin'); carregarAdminDashboard(); carregarAdminReservas(); carregarAdminQuartos();
}

async function adminAddQuarto() {
    const nome = document.getElementById('admin_q_nome').value; const banheiros = document.getElementById('admin_q_banheiros').value; const camas = document.getElementById('admin_q_camas').value; const pessoas = document.getElementById('admin_q_pessoas').value; const preco = document.getElementById('admin_q_preco').value; const imgInput = document.getElementById('admin_q_img'); 
    if(!nome || !banheiros || !camas || !pessoas || !preco) { mostrarToast("Preencha os dados básicos do quarto!", 'erro'); return; }

    let formData = new FormData();
    formData.append('nome', nome); formData.append('banheiros', banheiros); formData.append('camas', camas); formData.append('pessoas', pessoas); formData.append('preco', preco);
    if(imgInput.files.length > 0) { formData.append('imagem', imgInput.files[0]); }

    const res = await fetch('php/av2_api.php?action=admin_add_quarto', { method: 'POST', body: formData });
    const data = await res.json();
    if(data.sucesso) { mostrarToast("Quarto adicionado com sucesso!", 'sucesso'); carregarAdminQuartos(); }
}

async function carregarAdminDashboard() {
    const res = await fetch('php/av2_api.php?action=admin_dashboard'); const stats = await res.json();
    document.getElementById('admin_dashboard_cards').innerHTML = `
        <div class="card-quarto" style="text-align:center; background:#dcfce7;"><h3>Reservas Ativas</h3><h1 style="color:#22c55e; font-size:3em;">${stats.reservas_ativas}</h1></div>
        <div class="card-quarto" style="text-align:center; background:#fae8ff;"><h3>Receita Estimada</h3><h1 style="color:#c026d3; font-size:2.5em;">R$ ${stats.receita ? parseFloat(stats.receita).toFixed(2) : "0.00"}</h1></div>
    `;
}

async function carregarAdminReservas() {
    const res = await fetch('php/av2_api.php?action=admin_reservas'); const reservas = await res.json();
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

async function carregarAdminQuartos() {
    const res = await fetch('php/av2_api.php?action=quartos'); const quartos = await res.json();
    const div = document.getElementById('admin-lista-quartos'); div.innerHTML = '';
    const select = document.getElementById('admin_b_quarto'); select.innerHTML = '';
    quartos.forEach(q => {
        div.innerHTML += `<div class="card-quarto" style="text-align:center;"><h3>${q.nome}</h3><p>R$ ${q.preco}</p><button class="btn-vermelho" style="margin-top:10px;" onclick="adminDelQuarto(${q.id})">Remover</button></div>`;
        select.innerHTML += `<option value="${q.id}">${q.nome}</option>`;
    });
}

async function adminDelQuarto(quarto_id) { 
    if(confirm("Apagar quarto?")) { 
        const res = await fetch('php/av2_api.php?action=admin_del_quarto', { method: 'POST', body: JSON.stringify({ quarto_id }) }); 
        const data = await res.json();
        if(data.sucesso) { mostrarToast("Quarto removido com sucesso.", 'sucesso'); carregarAdminQuartos(); } 
        else { mostrarToast("Erro: " + data.erro, 'erro'); }
    } 
}

async function adminBloquearDatas() {
    const quarto_id = document.getElementById('admin_b_quarto').value; const data_inicio = document.getElementById('admin_b_inicio').value; const data_fim = document.getElementById('admin_b_fim').value;
    if(!quarto_id || !data_inicio || !data_fim) return;
    const res = await fetch('php/av2_api.php?action=admin_bloquear_datas', { method: 'POST', body: JSON.stringify({ quarto_id, data_inicio, data_fim }) });
    const data = await res.json();
    if(data.sucesso) { mostrarToast("Datas bloqueadas e conflitos resolvidos!", 'sucesso'); carregarAdminReservas(); } 
    else { mostrarToast("Erro: " + data.erro, 'erro'); }
}