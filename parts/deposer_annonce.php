<?php
session_start();
require_once "../database.php"; // connexion BDD

if(!isset($_SESSION['id_utilisateur'])){
header("Location: ../auth/connexion.php");
exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">
    <title>Déposer une annonce</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <?php include("header_parts.php"); ?>

</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar -->

            <div class="col-md-2 sidebar">
                 <div class="logo text-center mb-4">
                    <img src="../images/logo_upcycle.png" alt="Logo Upcycle">
                </div>

                <a href="dashboard_parts.php">Dashboard</a>
                <a href="deposer_annonce.php">Déposer une annonce</a>
                <a href="#">Dépôt conteneur</a>
                <a href="#">Formations / Événements</a>
                <a href="#">Mon planning</a>
                <a href="#">Conseils</a>
                <a href="#">Upcycling Score</a>
                <a href="#">Notifications</a>
                <a href="#">Mon profil</a>
            </div>

            <!-- Contenu -->

            <div class="col-md-10 p-5">

            <h2 class="page-title">Déposer une annonce</h2>

            <div class="form-container shadow mt-4">

            <form action="traitement_deposer_annonce.php" method="POST" enctype="multipart/form-data">                
                <div class="mb-3">
                    <label>Titre de l'objet</label>
                    <input type="text" name="titre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>

                <div class="mb-3">
                    <label>Type d'annonce</label>
                    <select name="type_annonce" class="form-control">
                        <option value="DON">Don</option>
                        <option value="VENTE">Vente</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Prix (si vente)</label>
                    <input type="number" name="prix" step="0.01" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Photo de l'objet</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <button class="btn btn-success" name="publier">
                    Publier l'annonce
                </button>

            </form>

            </div>

        </div>

    </div>
    <?php include("footer_parts.php"); ?>

</body>
</html>