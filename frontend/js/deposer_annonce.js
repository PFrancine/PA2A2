const API   = 'http://localhost:8080';
const token = localStorage.getItem('token');
const user  = JSON.parse(localStorage.getItem('user') || 'null');

if (!token || !user) window.location.href = 'connexion.html';

function authHeaders() {
    return {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    };
}

// ── Aperçu en temps réel ─────────────────────────────────────
const etatLabels = {
    TRES_BON:    ['badge-valide',  '✨ Très bon état'],
    BON:         ['badge-valide',  '👍 Bon état'],
    A_REPARER:   ['badge-attente', '🔧 À réparer'],
    POUR_PIECES: ['badge-refuse',  '⚙️ Pour pièces'],
};

function updatePreview() {
    const titre = document.getElementById('titre').value.trim();
    const desc  = document.getElementById('description').value.trim();
    const type  = document.getElementById('type_annonce').value;
    const etat  = document.getElementById('etat').value;
    const prix  = document.getElementById('prix').value;

    // Titre
    const recapTitre = document.getElementById('recapTitre');
    recapTitre.textContent = titre || 'Titre de votre annonce';
    recapTitre.classList.toggle('has-content', !!titre);

    // Description
    document.getElementById('recapDesc').textContent = desc || 'La description apparaîtra ici...';

    // Type badge
    const typeEl = document.getElementById('recapType');
    if (type === 'DON') {
        typeEl.className = 'badge badge-don';
        typeEl.textContent = '🎁 Don';
    } else {
        typeEl.className = 'badge badge-vente';
        typeEl.textContent = '💰 Vente';
    }

    // État badge
    const [cls, label] = etatLabels[etat] || ['badge-attente', etat];
    const etatEl = document.getElementById('recapEtat');
    etatEl.className = `badge ${cls}`;
    etatEl.textContent = label;

    // Prix
    const prixEl = document.getElementById('recapPrix');
    if (type === 'VENTE' && prix) {
        prixEl.textContent = parseFloat(prix).toFixed(2) + ' €';
        prixEl.style.display = 'block';
    } else {
        prixEl.style.display = 'none';
    }
}

// ── Compteur description ─────────────────────────────────────
document.getElementById('description').addEventListener('input', function () {
    document.getElementById('charCount').textContent = this.value.length;
    updatePreview();
});

// ── Afficher/masquer champ prix ──────────────────────────────
function togglePrix() {
    const type = document.getElementById('type_annonce').value;
    const champPrix = document.getElementById('champPrix');
    champPrix.style.display = type === 'VENTE' ? 'block' : 'none';
    updatePreview();
}

// ── Sélecteur d'état visuel ──────────────────────────────────
document.querySelectorAll('.etat-step').forEach(step => {
    step.addEventListener('click', () => {
        document.querySelectorAll('.etat-step').forEach(s => s.classList.remove('active'));
        step.classList.add('active');
        document.getElementById('etat').value = step.dataset.val;
        updatePreview();
    });
});

document.getElementById('etat').addEventListener('change', function () {
    const val = this.value;
    document.querySelectorAll('.etat-step').forEach(s => {
        s.classList.toggle('active', s.dataset.val === val);
    });
    updatePreview();
});

// ── Écouter les autres champs ────────────────────────────────
['titre', 'prix', 'type_annonce'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', updatePreview);
    if (el) el.addEventListener('change', updatePreview);
});

// ── Soumission ───────────────────────────────────────────────
async function handleSubmit() {
    const titre       = document.getElementById('titre').value.trim();
    const description = document.getElementById('description').value.trim();
    const type_annonce = document.getElementById('type_annonce').value;
    const etat        = document.getElementById('etat').value;
    const materiaux   = document.getElementById('materiaux').value || null;
    const poids_kg    = parseFloat(document.getElementById('poids_kg').value) || null;
    const prix        = type_annonce === 'VENTE'
        ? parseFloat(document.getElementById('prix').value) || 0
        : 0;

    // Masquer messages
    document.getElementById('errorMsg').style.display   = 'none';
    document.getElementById('successMsg').style.display = 'none';

    // Validation
    if (!titre)       { showMsg('error', 'Le titre est obligatoire.'); return; }
    if (!description) { showMsg('error', 'La description est obligatoire.'); return; }
    if (type_annonce === 'VENTE' && prix <= 0) {
        showMsg('error', 'Veuillez indiquer un prix valide pour une annonce de vente.');
        return;
    }

    setLoading(true);

    try {
        const body = { titre, description, type_annonce, etat, prix, materiaux, poids_kg };

        const res  = await fetch(`${API}/annonces`, {
            method: 'POST',
            headers: authHeaders(),
            body: JSON.stringify(body)
        });
        const data = await res.json();

        if (!res.ok) {
            const msg = Array.isArray(data) ? data.join('\n') : (data?.message || 'Erreur lors de la publication.');
            showMsg('error', msg);
            return;
        }

        showMsg('success', '🎉 Annonce déposée avec succès ! Elle sera examinée par notre équipe.');
        setTimeout(() => window.location.href = 'mes_annonces.html', 1800);

    } catch {
        showMsg('error', 'Impossible de joindre le serveur. L\'API est-elle démarrée ?');
    } finally {
        setLoading(false);
    }
}

// ── Helpers ──────────────────────────────────────────────────
function setLoading(loading) {
    document.getElementById('btnSubmit').disabled = loading;
    document.getElementById('spinner').style.display = loading ? 'block' : 'none';
    document.getElementById('btnText').textContent = loading ? 'Publication...' : 'Publier l\'annonce';
}

function showMsg(type, text) {
    const id    = type === 'success' ? 'successMsg' : 'errorMsg';
    const other = type === 'success' ? 'errorMsg'   : 'successMsg';
    document.getElementById(id).textContent      = text;
    document.getElementById(id).style.display    = 'block';
    document.getElementById(other).style.display = 'none';
    document.getElementById(id).scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = 'connexion.html';
}

// ── Init ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', updatePreview);
