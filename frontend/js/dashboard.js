const API = 'http://localhost:8080';

// ── Auth guard ───────────────────────────────────────────────
const token = localStorage.getItem('token');
const user  = JSON.parse(localStorage.getItem('user') || 'null');

//if (!token || !user) window.location.href = 'connexion.html';

// ── Helpers ──────────────────────────────────────────────────
function authHeaders() {
    return {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    };
}

function badgeStatut(statut) {
    const map = {
        'EN_ATTENTE': ['badge-attente', '⏳ En attente'],
        'VALIDE':     ['badge-valide',  '✅ Validée'],
        'REFUSE':     ['badge-refuse',  '❌ Refusée'],
    };
    const [cls, label] = map[statut] || ['badge-attente', statut];
    return `<span class="badge ${cls}">${label}</span>`;
}

function badgeType(type) {
    return type === 'DON'
        ? `<span class="badge badge-don">🎁 Don</span>`
        : `<span class="badge badge-vente">💰 Vente</span>`;
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return {
        day:   d.getDate(),
        month: d.toLocaleString('fr-FR', { month: 'short' }),
        full:  d.toLocaleDateString('fr-FR', { day:'2-digit', month:'long', year:'numeric' })
    };
}

// ── Init ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Nom de l'utilisateur
    const prenom = user?.prenom || 'Invité';
    document.getElementById('helloUser').textContent = `Bonjour, ${prenom} 👋`;

    loadAnnonces();
    loadEvenements();
});

// ── Annonces ─────────────────────────────────────────────────
async function loadAnnonces() {
    try {
        const res  = await fetch(`${API}/annonces/mes-annonces`, { headers: authHeaders() });
        const data = await res.json();

        const annonces = Array.isArray(data) ? data : [];

        // Stats
        document.getElementById('statAnnonces').textContent = annonces.length;
        document.getElementById('statValidees').textContent = annonces.filter(a => a.statut === 'VALIDE').length;
        document.getElementById('statAttente').textContent  = annonces.filter(a => a.statut === 'EN_ATTENTE').length;

        // Grille — 3 dernières
        const grid    = document.getElementById('annoncesGrid');
        const recents = annonces.slice(0, 3);

        if (recents.length === 0) {
            grid.innerHTML = `
                <div class="empty-state" style="grid-column:1/-1">
                    <span>📭</span>
                    Vous n'avez pas encore d'annonces.<br>
                    <a href="deposer_annonce.html" style="color:var(--green);font-weight:600">Déposer une annonce →</a>
                </div>`;
            return;
        }

        grid.innerHTML = recents.map(a => `
            <div class="annonce-card">
                <div class="annonce-card-body">
                    <div class="annonce-titre">${a.titre}</div>
                    <div class="annonce-meta">
                        ${badgeStatut(a.statut)}
                        ${badgeType(a.type_annonce)}
                        ${a.prix > 0 ? `<span style="font-size:13px;color:var(--muted)">${a.prix} €</span>` : ''}
                    </div>
                </div>
            </div>
        `).join('');

    } catch (err) {
        document.getElementById('annoncesGrid').innerHTML =
            `<p style="color:var(--error);grid-column:1/-1">Erreur de chargement des annonces.</p>`;
    }
}

// ── Événements ───────────────────────────────────────────────
async function loadEvenements() {
    try {
        const res  = await fetch(`${API}/evenements`);
        const data = await res.json();

        const evenements = Array.isArray(data) ? data : [];

        document.getElementById('statEvents').textContent = evenements.length;

        const list    = document.getElementById('eventsList');
        const prochains = evenements.slice(0, 3);

        if (prochains.length === 0) {
            list.innerHTML = `
                <div class="empty-state">
                    <span>📅</span>Aucun événement à venir.
                </div>`;
            return;
        }

        list.innerHTML = prochains.map(e => {
            const d = formatDate(e.date_event);
            return `
                <div class="event-item">
                    <div class="event-date">
                        <div class="event-date-day">${d.day}</div>
                        <div class="event-date-month">${d.month}</div>
                    </div>
                    <div class="event-info">
                        <div class="event-titre">${e.titre}</div>
                        <div class="event-meta">${e.type_event} ${e.lieu ? '· ' + e.lieu : ''}</div>
                    </div>
                    <div class="event-prix">${e.prix > 0 ? e.prix + ' €' : 'Gratuit'}</div>
                </div>`;
        }).join('');

    } catch (err) {
        document.getElementById('eventsList').innerHTML =
            `<p style="color:var(--error)">Erreur de chargement des événements.</p>`;
    }
}

// ── Déconnexion ──────────────────────────────────────────────
function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = 'connexion.html';
}
