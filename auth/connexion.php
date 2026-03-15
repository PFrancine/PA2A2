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

<head>

<meta charset="UTF-8">
<title>Connexion</title>

<link rel="stylesheet" href="style_connexion.css?v=999">

</head>

<body>

<header>

<img src="../images/logo.png" class="logo">

<input type="text" placeholder="Rechercher...">

<div class="icons">

🔍
👤
☰

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