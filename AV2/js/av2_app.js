// js/av2_app.js

async function inicializarSite() {
    try {
        // Verifica quem está logado e define os menus
        await verificarSessao();
        
        // Esconde todas as telas e mostra a Home
        mudarTela('home');

    } catch (erro) {
        console.error("Erro ao inicializar o site:", erro);
    }
}

// Inicia o processo assim que o script é lido
inicializarSite();