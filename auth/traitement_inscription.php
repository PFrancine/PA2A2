<?php
session_start();

$host = "localhost";
$dbname = "upcycleconnect";
$user = "root";
$password = "";

$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",$user,$password);

/* récupération données */

$prenom = $_POST['prenom'];
$nom = $_POST['nom'];
$email = $_POST['email'];
$mdp = $_POST['password'];
$confirm = $_POST['confirm_password'];
$role = $_POST['role'];
$captcha = $_POST['captcha'];


/* vérification mot de passe */

if($mdp != $confirm){
die("Les mots de passe ne correspondent pas");
}


/* vérification captcha */

if($captcha != $_SESSION['captcha']){
die("Captcha incorrect");
}


/* hash mot de passe */

$mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);


/* insertion base */

$sql = "INSERT INTO utilisateurs(prenom,nom,email,mot_de_passe,role)
VALUES(?,?,?,?,?)";

$stmt = $conn->prepare($sql);

$stmt->execute([$prenom,$nom,$email,$mdp_hash,$role]);

header("Location: connexion.php");
?>