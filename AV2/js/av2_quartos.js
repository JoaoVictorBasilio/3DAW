// AV2 file: Comportamento de listagem e filtragem de quartos no lado do cliente.
async function carregarQuartos() {
    const res = await fetch('php/av2_api.php?action=quartos');
    listaDeQuartosGlobal = await res.json();
    renderizarQuartos(listaDeQuartosGlobal);
}

function filtrarQuartos() {
    const maxPreco = parseFloat(document.getElementById('filtro_preco').value) || Infinity;
    const minPessoas = parseInt(document.getElementById('filtro_pessoas').value) || 0;
    
    const quartosFiltrados = listaDeQuartosGlobal.filter(q => q.preco <= maxPreco && q.pessoas >= minPessoas);
    renderizarQuartos(quartosFiltrados);
}

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