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

    // Vérifier si les mots de passe correspondent
    if($password != $confirm){
        $message = "Les mots de passe ne correspondent pas.";
    }
    // Vérifier la longueur
    elseif(strlen($password) < 8){
        $message = "Le mot de passe doit contenir au moins 8 caractères.";
    }
    // Vérifier présence d'une majuscule
    elseif(!preg_match('/[A-Z]/', $password)){
        $message = "Le mot de passe doit contenir au moins une lettre majuscule.";
    }
    // Vérifier présence d'un chiffre
    elseif(!preg_match('/[0-9]/', $password)){
        $message = "Le mot de passe doit contenir au moins un chiffre.";
    }
    // Vérifier présence d'un caractère spécial
    elseif(!preg_match('/[\W_]/', $password)){
        $message = "Le mot de passe doit contenir au moins un caractère spécial (ex: !@#$%).";
    }
    else {
        // Vérifier si l'email existe déjà
        $sql = "SELECT id_utilisateur FROM utilisateur WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        if($stmt->rowCount() > 0){
            $message = "Cet email existe déjà.";
        }
        else {
            // Hash du mot de passe
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insertion utilisateur
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

            // redirection vers la page de connexion
            header("Location: connexion.php");
            exit();
        }
    }

}
?>


<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<title>Inscription</title>

<link rel="stylesheet" href="style_inscription.css?v=999">

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