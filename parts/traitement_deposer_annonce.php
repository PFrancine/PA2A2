<?php
session_start();
require_once "../database.php";

if(!isset($_SESSION['id_utilisateur'])){
header("Location: ../auth/connexion.php");
exit();
}

if(isset($_POST['publier'])){

    $titre = htmlspecialchars($_POST['titre']);

    $categorie = $_POST['categorie'];
    if($categorie == "autre"){
    $categorie = $_POST['autre_categorie'];
    }

    $description = htmlspecialchars($_POST['description']);
    $type = $_POST['type_annonce'];
    if($type == "DON"){
    $prix = null;
    }else{
    $prix = $_POST['prix'];
    }
    $id_utilisateur = $_SESSION['id_utilisateur'];

    $statut = "EN_ATTENTE";

    $sql = "INSERT INTO annonce
    (titre, categorie, description, type_annonce, prix, statut, id_utilisateur)
    VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
    $titre,
    $categorie,
    $description,
    $type,
    $prix,
    $statut,
    $id_utilisateur
    ]);

    $id_annonce = $pdo->lastInsertId();
    $dossier = "../uploads/image_annonce/";
    $nbImages = min(count($_FILES['images']['tmp_name']), 5);
    for($i = 0; $i < $nbImages; $i++){

        if($_FILES['images']['error'][$i] == 0){

            $tmp_name = $_FILES['images']['tmp_name'][$i];

            $nomImage = uniqid() . "_" . $_FILES['images']['name'][$i];

            move_uploaded_file($tmp_name, $dossier . $nomImage);

            $sql = "INSERT INTO image_annonce (id_annonce, chemin_image)
                    VALUES (?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_annonce, $nomImage]);
        }
    }
    echo "<script>alert('Annonce envoyée pour validation');</script>";
    header("Location: dashboard_parts.php?success=1");
    exit();
}
?>