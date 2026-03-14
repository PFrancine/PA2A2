<?php
// inscription.php
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<title>Inscription</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header class="topbar">

<div class="logo">
♻<br>UPCYCLE
</div>

<div class="search-bar"></div>

<div class="top-icons">
<div class="icon">⌕</div>
<div class="icon">👤</div>

<div class="menu">
<span class="icon">☰</span>
MENU
</div>
</div>

</header>


<main class="page-wrapper">

<section class="form-card">

<h1>S'inscrire</h1>

<form method="POST">

<div class="row">

<div class="field-group">
<label>Prénom</label>
<input type="text" name="prenom" placeholder="Jean">
</div>

<div class="field-group">
<label>Nom de famille</label>
<input type="text" name="nom" placeholder="Dupont">
</div>

</div>


<label>Email</label>
<input type="email" name="email" placeholder="exemple@domaine.fr">


<label>Mot de passe</label>
<input type="password" name="password">
<p class="hint">Doit comporter au moins 6 caractères</p>


<label>Confirmez votre mot de passe</label>
<input type="password" name="confirm_password">


<label>Vous êtes :</label>
<select name="role">
<option>Veuillez choisir une réponse</option>
<option>Particulier</option>
<option>Professionnel</option>
<option>Association</option>
<option>Entreprise</option>
</select>


<label>Résolvez ce problème et renseignez le résultat :</label>

<div class="math-row">

<div class="operation-box">
30 - 4
</div>

<span>Entrez votre réponse :</span>

<input class="math-input" type="text" name="captcha">

</div>


<button class="submit-btn" type="submit">
Se connecter
</button>


<p class="login-link">
Vous avez déjà un compte ?
<a href="#">Se connecter maintenant</a>
</p>

</form>

</section>

</main>

</body>
</html>