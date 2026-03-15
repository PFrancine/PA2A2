<?php
session_start();
require_once __DIR__ . '/database.php';

// Récupérer 1 formation
$sqlFormation = "SELECT * FROM formation WHERE statut='VALIDE' ORDER BY date_formation LIMIT 1";
$formation = $pdo->query($sqlFormation)->fetch();

// Récupérer 2 événements
$sqlEvenement = "SELECT * FROM evenement ORDER BY date_evenement LIMIT 2";
$evenements = $pdo->query($sqlEvenement)->fetchAll();

// Récupérer 4 annonces pour le forum
$sqlForum = "SELECT titre, date_publication FROM annonce ORDER BY date_publication DESC LIMIT 4";
$annonces = $pdo->query($sqlForum)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>UpcycleConnect - Accueil</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- =========================
HEADER PREMIUM
========================= -->
<header class="navbar">
    <div class="nav-left">
        <img src="images/logo_upcycle.png" class="logo" alt="Logo">
        <span class="brand">UpcycleConnect</span>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="#conseils">Conseils & Astuces</a>
            <a href="#catalogue">Catalogue</a>
            <a href="#forum">Forum</a>
        </nav>
    </div>

    <div class="nav-search">
        <input type="text" placeholder="Rechercher un objet, une formation...">
    </div>

    <div class="nav-right">
        <span class="notification">🔔</span>
        <div class="profile">
            👤
            <div class="dropdown">
                <?php if(isset($_SESSION['id_utilisateur'])): ?>
                    <a href="profil.php">Profil</a>
                    <a href="auth/logout.php">Déconnexion</a>
                <?php else: ?>
                    <a href="auth/connexion.php">Connexion</a>
                    <a href="auth/inscription.php">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- =========================
HERO
========================= -->
<section class="hero">
    <div class="hero-text">
        <h1>Du passé au renouveau,<br>créons un monde plus beau !</h1>
        <p>Rejoignez notre communauté d’upcycling et donnez une seconde vie aux objets.</p>
        <a href="auth/connexion.php" class="btn">Découvrir</a>
    </div>
    <div class="hero-img">
        <img src="images/recyclage.jpeg" alt="Upcycling">
    </div>
</section>

<!-- =========================
PRESENTATION
========================= -->
<section class="presentation" id="presentation">
    <h2>Découvrez UpCycle</h2>
    <p>
    Chez UpcycleConnect, nous sommes dédiés à la valorisation des matériaux et à la réduction des déchets par l’upcycling.
    </p>
    <p>Nous réunissons particuliers, artisans et entreprises pour valoriser les matériaux et réduire les déchets.</p>
    <p>
    Grâce à notre espace collaboratif, chacun peut proposer, trouver et suivre des projets créatifs et durables.
    </p>
    <p>
    Chaque initiative contribue à un impact environnemental mesurable.
    Ensemble, créons un monde plus beau.
    </p>   

</section>

<!-- =========================
CONSEILS
========================= -->
<section class="conseils" id="conseils">
    <h2>Conseils & Astuces</h2>
    <div class="cards">
        <div class="card">
            <img src="images/bocal.png" alt="Recycler un bocal">
            <h3>Recycler un bocal en verre</h3>
            <p>Transformez un bocal en pot de rangement pour vos épices ou vos vis.</p>
        </div>
        <div class="card">
            <img src="images/chaise.png" alt="Seconde vie chaise">
            <h3>Donner une seconde vie à une chaise</h3>
            <p>Une chaise cassée peut devenir un support pour plantes.</p>
        </div>
    </div>
</section>

<!-- =========================
CATALOGUE
========================= -->
<section class="catalogue" id="catalogue">
    <h2>Catalogue (Formations / Événements)</h2>
    <div class="cards">

        <?php if($formation): ?>
        <div class="card">
            <h3>Formation</h3>
            <h4><?= $formation['titre'] ?></h4>
            <p><?= substr($formation['description'],0,100) ?>...</p>
            <a href="catalogue/formation.php?id=<?= $formation['id_formation'] ?>">En savoir plus</a>
        </div>
        <?php endif; ?>

        <?php foreach($evenements as $event): ?>
        <div class="card">
            <h3>Événement</h3>
            <h4><?= $event['titre'] ?></h4>
            <p><?= substr($event['description'],0,100) ?>...</p>
            <a href="catalogue/evenement.php?id=<?= $event['id_evenement'] ?>">En savoir plus</a>
        </div>
        <?php endforeach; ?>

    </div>
</section>

<!-- =========================
CONNEXION RAPIDE
========================= -->
<?php if(!isset($_SESSION['id_utilisateur'])): ?>
<section class="connexion">
    <h2>Déjà membre ?</h2>
    <form action="auth/connexion.php" method="POST">
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Mot de passe</label>
        <input type="password" name="password" required>
        <button type="submit">Se connecter</button>
        <p>Vous n'avez pas encore de compte ? <a href="auth/inscription.php">Inscrivez-vous</a></p>
    </form>
</section>
<?php endif; ?>

<!-- =========================
FORUM
========================= -->
<section class="forum" id="forum">
    <h2>Forum</h2>
    <h3>Actualités :</h3>
    <?php foreach($annonces as $annonce): ?>
    <div class="post">
        <p><?= $annonce['titre'] ?></p>
        <span><?= date('d M Y', strtotime($annonce['date_publication'])) ?></span>
    </div>
    <?php endforeach; ?>
    <h3>Question du jour</h3>
    <p>Que peut-on faire avec des bouteilles en verre recyclées ?</p>
    <a href="auth/connexion.php" class="btn">Accéder au Forum</a>
</section>

<!-- =========================
FOOTER PREMIUM
========================= -->
<footer>
    <div class="footer-content">
        <h3>UpcycleConnect</h3>
        <p>174 rue La Fayette, 75010 Paris</p>
        <p>Email : contact@upcycleconnect.fr</p>
        <p>Hébergeur : AWS</p>
    </div>
</footer>

</body>
</html> 