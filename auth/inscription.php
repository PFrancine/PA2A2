<?php
require_once "../database.php";

$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

$prenom = htmlspecialchars($_POST["prenom"]);
$nom = htmlspecialchars($_POST["nom"]);
$email = htmlspecialchars($_POST["email"]);
$password = $_POST["password"];
$confirm = $_POST["confirm_password"];
$role = $_POST["role"];

/* vérifier mots de passe */

if($password != $confirm){
$message = "Les mots de passe ne correspondent pas.";
}

else{

/* vérifier si email existe */

$sql = "SELECT id_utilisateur FROM utilisateur WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);

if($stmt->rowCount() > 0){
$message = "Cet email existe déjà.";
}

else{

/* hash mot de passe */

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

/* insertion utilisateur */

$sqlInsert = "INSERT INTO utilisateur 
(nom, prenom, email, mot_de_passe, id_role)
VALUES (?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sqlInsert);

$stmt->execute([
$nom,
$prenom,
$email,
$passwordHash,
$role
]);

$message = "Inscription réussie !";

}

}

}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<title>Inscription</title>

<link rel="stylesheet" href="style_inscription.css">

</head>

<body>

<section class="inscription-container">

<h2>S’inscrire</h2>

<?php if($message): ?>
<p><?= $message ?></p>
<?php endif; ?>

<form method="POST">

<div class="row">

<div class="input-group">
<label>Prénom</label>
<input type="text" name="prenom" required>
</div>

<div class="input-group">
<label>Nom</label>
<input type="text" name="nom" required>
</div>

</div>

<label>Email</label>
<input type="email" name="email" required>

<label>Mot de passe</label>
<input type="password" name="password" required>

<label>Confirmer mot de passe</label>
<input type="password" name="confirm_password" required>

<label>Vous êtes :</label>

<select name="role" required>

<option value="">Choisir</option>
<option value="1">Particulier</option>
<option value="2">Professionnel</option>
<option value="3">Salarié</option>

</select>

<button type="submit">S'inscrire</button>

<p>
Déjà inscrit ?  
<a href="connexion.php">Se connecter</a>
</p>

</form>

</section>

</body>
</html>