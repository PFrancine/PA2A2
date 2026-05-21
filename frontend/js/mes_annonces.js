const API   = 'http://localhost:8080';
const token = localStorage.getItem('token');
const user  = JSON.parse(localStorage.getItem('user') || 'null');

if (!token || !user) window.location.href = '../connexion.html';

function authHeaders() {
    return { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` };
}

// ── État ─────────────────────────────────────────────────────
let allAnnonces  = [];
let filtreActuel = 'all';
let deleteId     = null;

// ── Init ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    loadAnnonces();

    // Filtres
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filtreActuel = btn.dataset.filter;
            renderTable();
        });
    });
});

// ── Chargement ───────────────────────────────────────────────
async function loadAnnonces() {
    try {
        const res  = await fetch(`${API}/annonces/mes-annonces`, { headers: authHeaders() });
        const data = await res.json();
        allAnnonces = Array.isArray(data) ? data : [];
        renderTable();
    } catch {
        showMsg('error', 'Impossible de charger les annonces.');
        document.getElementById('annoncesList').innerHTML = '';
    }
}

// ── Rendu table ──────────────────────────────────────────────
function renderTable() {
    const list = document.getElementById('annoncesList');

    const filtered = filtreActuel === 'all'
        ? allAnnonces
        : allAnnonces.filter(a => a.statut === filtreActuel);

    if (filtered.length === 0) {
        list.innerHTML = `
            <div class="empty-state">
                <span>📭</span>
                Aucune annonce trouvée.
                <br><a href="deposer_annonce.html" style="color:var(--green);font-weight:600">Déposer une annonce →</a>
            </div>`;
        return;
    }

    list.innerHTML = `
        <div class="annonces-table">
            <div class="table-head">
                <span>Titre</span>
                <span>Type</span>
                <span>Prix</span>
                <span>Statut</span>
                <span></span>
            </div>
            ${filtered.map(a => `
                <div class="table-row">
                    <div>
                        <div class="row-titre">${a.titre}</div>
                        <div class="row-date">${formatDate(a.date_publication)}</div>
                    </div>
                    <div>${badgeType(a.type_annonce)}</div>
                    <div style="font-weight:600;color:var(--text)">
                        ${a.prix > 0 ? a.prix + ' €' : '–'}
                    </div>
                    <div>${badgeStatut(a.statut)}</div>
                    <div class="row-actions">
                        <button class="btn-icon btn-delete" onclick="openModal('${a.id}')" title="Supprimer">🗑</button>
                    </div>
                </div>
            `).join('')}
        </div>`;
}

// ── Helpers badges ───────────────────────────────────────────
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
    return new Date(dateStr).toLocaleDateString('fr-FR', {
        day: '2-digit', month: 'short', year: 'numeric'
    });
}

// ── Suppression ──────────────────────────────────────────────
function openModal(id) {
    deleteId = id;
    document.getElementById('modalOverlay').classList.add('open');
}

function closeModal() {
    deleteId = null;
    document.getElementById('modalOverlay').classList.remove('open');
}

async function confirmDelete() {
    if (!deleteId) return;

    try {
        const res = await fetch(`${API}/annonces/${deleteId}`, {
            method: 'DELETE',
            headers: authHeaders()
        });

        if (!res.ok) {
            showMsg('error', 'Impossible de supprimer l\'annonce.');
            return;
        }

        allAnnonces = allAnnonces.filter(a => a.id !== deleteId);
        renderTable();
        showMsg('success', 'Annonce supprimée avec succès.');

    } catch {
        showMsg('error', 'Erreur lors de la suppression.');
    } finally {
        closeModal();
    }
}

// ── Messages ─────────────────────────────────────────────────
function showMsg(type, text) {
    const el = document.getElementById(type === 'success' ? 'successMsg' : 'errorMsg');
    const other = document.getElementById(type === 'success' ? 'errorMsg' : 'successMsg');
    el.textContent = text;
    el.style.display = 'block';
    other.style.display = 'none';
    setTimeout(() => el.style.display = 'none', 4000);
}

// ── Déconnexion ──────────────────────────────────────────────
function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = '../connexion.html';
}

// Fermer modal en cliquant dehors
document.getElementById('modalOverlay').addEventListener('click', e => {
    if (e.target === document.getElementById('modalOverlay')) closeModal();
});