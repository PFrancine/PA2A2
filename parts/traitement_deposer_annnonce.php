<?php
session_start();
require_once "../database.php";

if(!isset($_SESSION['id_utilisateur'])){
header("Location: ../auth/connexion.php");
exit();
}

if(isset($_POST['publier'])){

    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);
    $type = $_POST['type_annonce'];
    $prix = $_POST['prix'];
    $id_utilisateur = $_SESSION['id_utilisateur'];

    $statut = "EN_ATTENTE";

    $sql = "INSERT INTO annonce
    (titre, description, type_annonce, prix, statut, id_utilisateur)
    VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
    $titre,
    $description,
    $type,
    $prix,
    $statut,
    $id_utilisateur
    ]);

    echo "<script>alert('Annonce envoyée pour validation');</script>";
    header("Location: dashboard_particulier.php?success=1");
    exit();
}
?>