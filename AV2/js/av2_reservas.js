// AV2 file: Lógica de fluxo de reservas e pagamento, incluindo reserva e cancelamento.
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

function mudarFormaPagamento() {
    const metodo = document.getElementById('pag_metodo').value;
    document.getElementById('bloco_cartao').style.display = metodo === 'pix' ? 'none' : 'block';
    document.getElementById('bloco_pix').style.display = metodo === 'pix' ? 'block' : 'none';
}

async function finalizarReserva() {
    const quarto_id = document.getElementById('pag_quarto_id').value;
    const data_inicio = document.getElementById('pag_checkin').value;
    const data_fim = document.getElementById('pag_checkout').value;
    const cpf = document.getElementById('pag_cpf').value;

    if(!data_inicio || !data_fim || !cpf) { mostrarToast("Preencha as datas e o CPF!", 'erro'); return; }

    const res = await fetch('php/av2_api.php?action=reservar', { method: 'POST', body: JSON.stringify({ quarto_id, data_inicio, data_fim, cpf }) });
    const data = await res.json();
    if (data.sucesso) { 
        mostrarToast("Reserva confirmada com sucesso!", 'sucesso'); 
        mudarTela('reservas'); carregarReservas(); 
    } else { 
        mostrarToast(data.erro, 'erro'); 
    }
}

async function carregarReservas() {
    if(!usuarioLogado) return;
    const res = await fetch('php/av2_api.php?action=minhas_reservas');
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
                <p><strong>Data de Entrada:</strong> ${inicio} (A partir das 14h)</p>
                <p><strong>Data de Saída:</strong> ${fim} (Até às 12h)</p>
            </div>
            <p style="margin-top: 30px; font-size: 0.9em; color:#555;">Apresente este documento na recepção do hotel.</p>
            <button onclick="window.print()" style="margin-top:20px; padding:10px 20px; background:#8a2be2; color:white; border:none; border-radius:5px; cursor:pointer;">Imprimir</button>
        </body></html>
    `);
    janela.document.close();
}

function abrirAvaliacao(quarto_id) {
    document.getElementById('aval_quarto_id').value = quarto_id;
    mudarTela('avaliacao_tela');
}

async function enviarAvaliacao() {
    const quarto_id = document.getElementById('aval_quarto_id').value; const nota = document.getElementById('aval_nota').value; const comentario = document.getElementById('aval_comentario').value;
    const res = await fetch('php/av2_api.php?action=avaliar_quarto', { method: 'POST', body: JSON.stringify({ quarto_id, nota, comentario }) });
    const data = await res.json();
    if(data.sucesso) { 
        mostrarToast("Obrigado pela sua avaliação!", 'sucesso'); mudarTela('reservas'); carregarQuartos(); 
    }
}

async function cancelarReserva(reserva_id) {
    if(confirm("Cancelar reserva?")) {
        await fetch('php/av2_api.php?action=cancelar_reserva', { method: 'POST', body: JSON.stringify({ reserva_id }) });
        mostrarToast("Reserva cancelada.", 'info'); carregarReservas();
    }
}