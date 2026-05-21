const API = 'http://localhost:8080';

// ── Utilitaires ──────────────────────────────────────────────
function showError(msg) {
    const el = document.getElementById('errorMsg');
    el.textContent = typeof msg === 'string' ? msg : JSON.stringify(msg);
    el.style.display = 'block';
    document.getElementById('successMsg').style.display = 'none';
}

function showSuccess(msg) {
    const el = document.getElementById('successMsg');
    el.textContent = msg;
    el.style.display = 'block';
    document.getElementById('errorMsg').style.display = 'none';
}

function setLoading(btnId, spinnerId, textId, loading, label) {
    document.getElementById(btnId).disabled = loading;
    document.getElementById(spinnerId).style.display = loading ? 'block' : 'none';
    document.getElementById(textId).textContent = label;
}

// Redirection si déjà connecté
if (localStorage.getItem('token')) {
    window.location.href = '../dashboard.html';
}

// ── Login ────────────────────────────────────────────────────
async function handleLogin() {
    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;

    document.getElementById('errorMsg').style.display   = 'none';
    document.getElementById('successMsg').style.display = 'none';

    if (!email || !password) { showError('Veuillez remplir tous les champs.'); return; }

    setLoading('btnLogin', 'spinner', 'btnText', true, 'Connexion...');

    try {
        const res  = await fetch(`${API}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, mot_de_passe: password })
        });
        const data = await res.json();

        if (!res.ok) { showError(data || 'Identifiants incorrects.'); return; }

        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));

        showSuccess('Connexion réussie ! Redirection...');
        setTimeout(() => window.location.href = 'dashboard.html', 800);

    } catch {
        showError('Impossible de joindre le serveur. L\'API est-elle démarrée ?');
    } finally {
        setLoading('btnLogin', 'spinner', 'btnText', false, 'Se connecter');
    }
}

// ── Register ─────────────────────────────────────────────────
async function handleRegister() {
    const nom      = document.getElementById('nom')?.value.trim();
    const prenom   = document.getElementById('prenom')?.value.trim();
    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const role     = parseInt(document.getElementById('role')?.value);
    const site     = parseInt(document.getElementById('site')?.value);

    document.getElementById('errorMsg').style.display   = 'none';
    document.getElementById('successMsg').style.display = 'none';

    if (!nom || !prenom || !email || !password) { showError('Veuillez remplir tous les champs.'); return; }

    setLoading('btnRegister', 'spinner', 'btnText', true, 'Création...');

    try {
        const res  = await fetch(`${API}/register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nom, prenom, email, mot_de_passe: password, id_role: role, id_site: site })
        });
        const data = await res.json();

        if (!res.ok) {
            const msg = Array.isArray(data) ? data.join('\n') : (data || 'Erreur lors de l\'inscription.');
            showError(msg); return;
        }

        showSuccess('Compte créé ! Redirection...');
        setTimeout(() => window.location.href = '../connexion.html', 1200);

    } catch {
        showError('Impossible de joindre le serveur. L\'API est-elle démarrée ?');
    } finally {
        setLoading('btnRegister', 'spinner', 'btnText', false, 'Créer mon compte');
    }
}

// Entrée au clavier
document.addEventListener('keydown', e => {
    if (e.key !== 'Enter') return;
    if (document.getElementById('btnLogin'))    handleLogin();
    if (document.getElementById('btnRegister')) handleRegister();
});
