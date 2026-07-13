// AV2 file: Lógica de inicialização que carrega templates de página e inicializa a SPA.
// Função para carregar todo o HTML modularizado
async function inicializarSite() {
    try {
        // 1. Carrega os componentes fixos (A navbar e o footer)
       document.getElementById('navbar-container').innerHTML = await (await fetch('html/components/navbar.html')).text();
        document.getElementById('footer-container').innerHTML = await (await fetch('html/components/footer.html')).text();

        // 2. Carrega todas as telas para dentro do paginas-container
        const paginas = ['home', 'quartos', 'contato', 'login', 'cadastro', 'reservas', 'perfil', 'pagamento', 'avaliacao', 'admin'];
        const containerPaginas = document.getElementById('paginas-container');
        
        for (const pagina of paginas) {
            const htmlDaPagina = await (await fetch(`html/pages/${pagina}.html`)).text();
            containerPaginas.innerHTML += htmlDaPagina;
        }

        // 3. Só depois de carregar todo o HTML, verifica quem está logado e define a tela inicial
        verificarSessao();
        mudarTela('home');

    } catch (erro) {
        console.error("Erro ao carregar as telas:", erro);
    }
}

// Inicia o processo assim que o script é lido
inicializarSite();