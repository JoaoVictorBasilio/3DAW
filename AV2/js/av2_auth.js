// AV2 file: Lógica de autenticação para login, cadastro, perfil e ações de conta.
async function verificarSessao() {
    const res = await fetch('php/av2_api.php?action=verificar_sessao');
    const data = await res.json();
    if (data.logado) {
        usuarioLogado = true;
        is_admin = data.is_admin;
        nomeHospedeGlobal = data.nome;
        atualizarMenuSuperior(data.nome);
    }
}

async function fazerLogin() {
    const email = document.getElementById('log_email').value.trim();
    const senha = document.getElementById('log_senha').value.trim();
    if (!email || !senha) return;

    const res = await fetch('php/av2_api.php?action=login', { method: 'POST', body: JSON.stringify({ email, senha }) });
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

async function fazerCadastro() {
    const nome = document.getElementById('cad_nome').value; const email = document.getElementById('cad_email').value; const senha = document.getElementById('cad_senha').value;
    if (!nome || !email || !senha) { mostrarToast("Preencha todos os campos do cadastro!", "erro"); return; }
    
    const res = await fetch('php/av2_api.php?action=cadastro', { method: 'POST', body: JSON.stringify({ nome, email, senha }) });
    const data = await res.json();
    if (data.sucesso) { 
        mostrarToast('Conta criada com sucesso! Faça login.', 'sucesso'); 
        mudarTela('login'); 
    } else { 
        mostrarToast(data.erro, 'erro'); 
    }
}

async function fazerLogout() {
    await fetch('php/av2_api.php?action=logout');
    usuarioLogado = false; is_admin = false; atualizarMenuSuperior(''); mudarTela('home'); 
    mostrarToast("Você saiu da conta.", 'info');
}

async function abrirPerfil() {
    if (!usuarioLogado) { mostrarToast("Faça login primeiro!", 'erro'); mudarTela('login'); return; }
    const res = await fetch('php/av2_api.php?action=carregar_perfil');
    const data = await res.json();
    if(data.erro) { mostrarToast(data.erro, 'erro'); return; }
    
    document.getElementById('perfil_nome').value = data.nome;
    document.getElementById('perfil_email').value = data.email;
    document.getElementById('perfil_senha').value = ''; 
    mudarTela('perfil');
}

async function salvarPerfil() {
    const nome = document.getElementById('perfil_nome').value.trim();
    const email = document.getElementById('perfil_email').value.trim();
    const senha = document.getElementById('perfil_senha').value.trim();
    
    if(!nome || !email) { mostrarToast("Nome e e-mail são obrigatórios!", 'erro'); return; }
    
    const res = await fetch('php/av2_api.php?action=editar_perfil', { method: 'POST', body: JSON.stringify({ nome, email, senha }) });
    const data = await res.json();
    
    if(data.sucesso) {
        mostrarToast("Perfil atualizado com sucesso!", 'sucesso');
        nomeHospedeGlobal = data.nome;
        atualizarMenuSuperior(data.nome);
    } else {
        mostrarToast("Erro: " + data.erro, 'erro');
    }
}

async function apagarConta() {
    if(confirm("ATENÇÃO: Tem certeza que deseja apagar sua conta permanentemente? Todas as suas reservas e avaliações serão excluídas e não poderão ser recuperadas.")) {
        const res = await fetch('php/av2_api.php?action=apagar_conta');
        const data = await res.json();
        if(data.sucesso) {
            mostrarToast("Sua conta foi apagada com sucesso.", 'sucesso');
            usuarioLogado = false; is_admin = false; atualizarMenuSuperior(''); mudarTela('home');
        } else {
            mostrarToast("Erro ao apagar: " + data.erro, 'erro');
        }
    }
}