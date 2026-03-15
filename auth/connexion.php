<?php

require_once "../database.php";

$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

$email = $_POST["email"];
$password = $_POST["password"];

$sql = "SELECT * FROM utilisateur WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);

$user = $stmt->fetch();

if($user){

if(password_verify($password, $user["mot_de_passe"])){

session_start();

$_SESSION["id_utilisateur"] = $user["id_utilisateur"];
$_SESSION["prenom"] = $user["prenom"];
$_SESSION["role"] = $user["id_role"];
if ($_SESSION['role'] == 1) {
    header('location: ../parts/dashboard_parts.php');
}
exit();

}

else{

$message = "Mot de passe incorrect";

}

}

else{

$message = "Email incorrect";

}

}
?>

<!DOCTYPE html>
<html lang="fr">

<header class="navbar">

<div class="nav-left">

<img src="../images/logo_upcycle.png" class="logo">

<span class="brand">UpcycleConnect</span>

<nav>

<a href="../index.php">Accueil</a>
<a href="../catalogue.php">Catalogue</a>
<a href="../formations.php">Formations</a>
<a href="../forum.php">Forum</a>

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

<a href="#">Profil</a>
<a href="#">Paramètres</a>
<a href="../auth/logout.php">Déconnexion</a>

</div>

</div>

</div>

</header>


<section class="login-container">

<h2>Se connecter</h2>

<?php if($message): ?>
<p class="error"><?= $message ?></p>
<?php endif; ?>

<form method="POST">

<label>Email</label>
<input type="email" name="email" required>

<label>Mot de passe</label>
<input type="password" name="password" required>

<button type="submit">Se connecter</button>

<p class="links">
<a href="#">Mot de passe oublié ?</a>
</p>

<p class="links">
Vous n'avez pas de compte ?
<a href="inscription.php">S'inscrire</a>
</p>

</form>

</section>

</body>
</html>