<?php
session_start();
require_once "../database.php";

if(!isset($_SESSION['id_utilisateur'])){
header("Location: ../auth/connexion.php");
exit();
}

if(!isset($_GET['id'])){
header("Location: mes_annonces.php");
exit();
}

$id_annonce = $_GET['id'];
$id_utilisateur = $_SESSION['id_utilisateur'];


$sql = "SELECT * FROM annonce
        WHERE id_annonce = ?
        AND id_utilisateur = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_annonce, $id_utilisateur]);

$annonce = $stmt->fetch();

if(!$annonce){
header("Location: mes_annonces.php");
exit();
}


$sqlImg = "SELECT chemin_image
           FROM image_annonce
           WHERE id_annonce = ?";

$stmtImg = $pdo->prepare($sqlImg);
$stmtImg->execute([$id_annonce]);

$images = $stmtImg->fetchAll();

/* supprimer les fichiers images */

foreach($images as $img){

$fichier = "../uploads/image_annonce/".$img['chemin_image'];

if(file_exists($fichier)){
unlink($fichier);
}

}


$sql = "DELETE FROM image_annonce
        WHERE id_annonce = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_annonce]);


$sql = "DELETE FROM annonce
        WHERE id_annonce = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_annonce]);

header("Location: mes_annonces.php?delete=success");
exit();
?>