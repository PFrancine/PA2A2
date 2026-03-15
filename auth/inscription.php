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

    if($password != $confirm){
        $message = "Les mots de passe ne correspondent pas.";
    } elseif(strlen($password) < 8){
        $message = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif(!preg_match('/[A-Z]/', $password)){
        $message = "Le mot de passe doit contenir au moins une lettre majuscule.";
    } elseif(!preg_match('/[0-9]/', $password)){
        $message = "Le mot de passe doit contenir au moins un chiffre.";
    } elseif(!preg_match('/[\W_]/', $password)){
        $message = "Le mot de passe doit contenir au moins un caractère spécial (ex: !@#$%).";
    } else {
        $sql = "SELECT id_utilisateur FROM utilisateur WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        if($stmt->rowCount() > 0){
            $message = "Cet email existe déjà.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $sqlInsert = "INSERT INTO utilisateur 
            (nom, prenom, email, mot_de_passe, id_role)
            VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sqlInsert);
            $stmt->execute([$nom, $prenom, $email, $passwordHash, $role]);
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription - UpcycleConnect</title>
<link rel="stylesheet" href="style_inscription.css">
</head>
<body>

<!-- =========================
BARRE DE NAVIGATION
========================= -->
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

<!-- =========================
FORMULAIRE INSCRIPTION
========================= -->
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