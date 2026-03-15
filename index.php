<?php
session_start();

// connexion à la base
require_once __DIR__ . '/database.php'; // __DIR__ = chemin absolu du fichier courant

// récupérer 1 formation
$sqlFormation = "SELECT * FROM formation WHERE statut='VALIDE' ORDER BY date_formation LIMIT 1";
$formation = $pdo->query($sqlFormation)->fetch();

// récupérer 2 événements
$sqlEvenement = "SELECT * FROM evenement ORDER BY date_evenement LIMIT 2";
$evenements = $pdo->query($sqlEvenement)->fetchAll();

// récupérer 4 annonces pour le forum
$sqlForum = "SELECT titre, date_publication FROM annonce ORDER BY date_publication DESC LIMIT 4";
$annonces = $pdo->query($sqlForum)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<title>UpcycleConnect</title>

<link rel="stylesheet" href="style.css">

</head>

<body>


<!-- HEADER -->

<header>

<img src="images/logo.png" class="logo">

<input type="text" placeholder="Rechercher...">

<div class="icons">

🔍

<a href="auth/inscription.php">👤</a>

☰

</div>

</header>


<!-- HERO -->

<section class="hero">

<div class="hero-text">

<h1>
Du passé au renouveau,<br>
créons un monde plus beau !
</h1>

</div>

<div class="hero-img">

<img src="images/recyclage.jpg">

</div>

</section>



<!-- PRESENTATION -->

<section class="presentation">

<h2>Découvrez UpCycle</h2>

<p>
Chez UpcycleConnect, nous sommes dédiés à la valorisation des matériaux et à la réduction des déchets par l’upcycling.
</p>

<p>
Nous réunissons particuliers, artisans et entreprises autour d’une même ambition : donner une seconde vie aux objets inutilisés.
</p>

<p>
Grâce à notre espace collaboratif, chacun peut proposer, trouver et suivre des projets créatifs et durables.
</p>

<p>
Chaque initiative contribue à un impact environnemental mesurable.
Ensemble, créons un monde plus beau.
</p>

</section>



<!-- CONSEILS -->

<section class="conseils">

<h2>Conseils & Astuces</h2>

<div class="cards">

<div class="card">

<img src="images/bocal.jpg">

<h3>Recycler un bocal en verre</h3>

<p>
Transformez un bocal en verre en pot de rangement pour vos épices ou vos vis.
</p>

</div>

<div class="card">

<img src="images/chaise.jpg">

<h3>Donner une seconde vie à une chaise</h3>

<p>
Une chaise cassée peut devenir un support pour plantes.
</p>

</div>

</div>

</section>



<!-- CATALOGUE -->

<section class="catalogue">

<h2>Catalogue (formations / événements)</h2>

<div class="cards">


<!-- Formation -->

<?php if($formation): ?>

<div class="card">

<h3>Formation</h3>

<h4><?= $formation['titre'] ?></h4>

<p>
<?= substr($formation['description'],0,80) ?>...
</p>

<a href="catalogue/formation.php?id=<?= $formation['id_formation'] ?>">
En savoir plus
</a>

</div>

<?php endif; ?>


<!-- Evenements -->

<?php foreach($evenements as $event): ?>

<div class="card">

<h3>Evenement</h3>

<h4><?= $event['titre'] ?></h4>

<p>
<?= substr($event['description'],0,80) ?>...
</p>

<a href="catalogue/evenement.php?id=<?= $event['id_evenement'] ?>">
En savoir plus
</a>

</div>

<?php endforeach; ?>


</div>

</section>



<!-- CONNEXION -->

<?php if(!isset($_SESSION['id_utilisateur'])): ?>

<section class="connexion">

<h2>Déjà membre ?</h2>

<form action="auth/login.php" method="POST">

<label>Email</label>
<input type="email" name="email" required>

<label>Mot de passe</label>
<input type="password" name="password" required>

<button type="submit">Se connecter</button>

<p>
Vous n'avez pas encore de compte ?
<a href="auth/inscription.php">Inscrivez-vous maintenant</a>
</p>

</form>

</section>

<?php endif; ?>



<!-- FORUM -->

<section class="forum">

<h2>Forum</h2>

<h3>Actualités :</h3>


<?php foreach($annonces as $annonce): ?>

<div class="post">

<p><?= $annonce['titre'] ?></p>

<span>
<?= date('d M Y', strtotime($annonce['date_publication'])) ?>
</span>

</div>

<?php endforeach; ?>


<h3>Question du jour</h3>

<p>
Que peut-on faire avec des bouteilles en verre recyclées ?
</p>

<a href="forum.php" class="btn">
Accéder au Forum
</a>

</section>



<!-- FOOTER -->

<footer>

<h3>Mentions légales</h3>

<p>
Nom : UpcycleConnect
</p>

<p>
Siège social : 174 rue La Fayette, 75010 Paris
</p>

<p>
Email : contact@upcycleconnect.fr
</p>

<p>
Hébergeur : AWS
</p>

</footer>


</body>
</html>