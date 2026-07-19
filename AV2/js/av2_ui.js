// AV2 file: Helpers de interface para alternar tema, notificações e navegação entre telas.
// === DARK MODE === //
if (localStorage.getItem('tema_almeidas') === 'dark') {
    document.body.classList.add('dark-theme');
}

function toggleDarkMode() {
    document.body.classList.toggle('dark-theme');
    if (document.body.classList.contains('dark-theme')) {
        localStorage.setItem('tema_almeidas', 'dark');
    } else {
        localStorage.setItem('tema_almeidas', 'light');
    }
}

// === NOTIFICAÇÕES TOAST === //
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

// === NAVEGAÇÃO E MENUS === //
function mudarTela(id) {
    document.querySelectorAll('.tela').forEach(t => t.classList.remove('ativa'));
    document.getElementById(id).classList.add('ativa');
}

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