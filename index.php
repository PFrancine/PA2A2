<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>UpcycleConnect</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<?php include("header.php"); ?>

<!-- HERO -->
<section class="hero">
<div class="hero-text">
<h1>Du passé au renouveau,<br>créons un monde plus beau !</h1>
</div>

<div class="hero-img">
<img src="images/recyclage.jpeg">
</div>
</section>


<!-- PRESENTATION -->
<section class="presentation">

<h2>Découvrez UpCycle</h2>

<p>
Chez UpcycleConnect, nous sommes dédiée à la valorisation des matériaux et à la réduction des déchets par l'upcycling.
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

<div class="card">

<h3>Formation</h3>

<h4>Réparer et customiser des vêtements</h4>

<img src="images/formation.jpg">

<a href="formation.php">En savoir plus</a>

</div>

<!-- CATALOGUE nouveau  test test -->

<div class="card">

<h3>Evenement</h3>

<h4>Rencontre avec des artisans recyclage</h4>

<img src="images/artisan.jpg">

<a href="evenement.php">En savoir plus</a>

</div>


<div class="card">

<h3>Evenement</h3>

<h4>Marché d’objets upcyclés</h4>

<img src="images/marche.jpg">

<a href="evenement.php">En savoir plus</a>

</div>

</div>

</section>


<!-- CONNEXION -->
<section class="connexion">

<h2>Déjà membres ?</h2>

<form action="login.php" method="POST">

<label>Email</label>
<input type="email" name="email" required>

<label>Mot de passe</label>
<input type="password" name="password" required>

<button type="submit">Se connecter</button>

<p><a href="forgot.php">Mot de passe oublié ?</a></p>

<p>Pas de compte ? <a href="auth/inscription.php">Inscrivez-vous</a></p>

</form>

</section>


<!-- FORUM -->
<section class="forum">

<h2>Forum</h2>

<h3>Actualités :</h3>

<div class="post">

<p>Comment transformer une vieille commode en meuble moderne ?</p>
<span>2109 réponses</span>

</div>

<div class="post">

<p>Où déposer des objets volumineux dans les conteneurs ?</p>
<span>903 réponses</span>

</div>

<div class="post">

<p>Idées pour recycler des palettes en meubles</p>
<span>302 réponses</span>

</div>

<div class="post">

<p>Avis sur l’atelier “Créer une lampe recyclée”</p>
<span>302 réponses</span>

</div>


<h3>Question du jour</h3>

<p>Que peut-on faire avec des bouteilles en verre recyclées ?</p>

<a href="forum.php" class="btn">Accéder au forum</a>

</section>


<?php include("footer.php"); ?>

</body>
</html>