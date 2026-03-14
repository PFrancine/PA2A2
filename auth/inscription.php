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

            $message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        }
    }

}
?>