<?php
session_start();
require_once "../database.php";

if(!isset($_SESSION['id_utilisateur'])){
header("Location: ../auth/connexion.php");
exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Espace Particulier - UpcycleConnect</title>
<?php include("header_parts.php"); ?>


</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar -->
                <?php include("sidebar_parts.php"); ?>
            <!-- Main content -->
            <div class="col-md-10 p-4">
                <?php
                    $prenom = $_SESSION['prenom'];
                ?>
                <h2 class="dashboard-title">Heyy <?php echo $prenom; ?> 👋 <br>
                 Bienvenue sur ton espace particulier  </h2>
                <p>Retrouvez ici vos activités et vos actions rapides.</p>

                <!-- Actions rapides -->

                <div class="row mt-4">

                    <div class="col-md-3">
                        <div class="card card-action p-3 text-center shadow">
                            <h5>Déposer un objet</h5>
                            <p>Publier une annonce</p>
                            <a href="deposer_annonce.php" class="btn btn-success">Créer</a>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card card-action p-3 text-center shadow">
                            <h5>Formations</h5>
                            <p>Voir les ateliers</p>
                            <button class="btn btn-success">Explorer</button>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card card-action p-3 text-center shadow">
                            <h5>Mon planning</h5>
                            <p>Voir mes activités</p>
                            <button class="btn btn-success">Consulter</button>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card card-action p-3 text-center shadow">
                            <h5>Upcycling Score</h5>
                            <p>Voir mon impact</p>
                            <button class="btn btn-success">Voir</button>
                        </div>
                    </div>
                </div>

                 <!-- Mes annonces -->

                <h4 class="mt-5">Mes dernières annonces</h4>

                <table class="table table-striped">

                <thead>
                <tr>
                <th>Titre</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Date</th>
                </tr>
                </thead>

                <tbody>

                <tr>
                <td>Chaise en bois</td>
                <td>Don</td>
                <td>En attente</td>
                <td>12/03/2026</td>
                </tr>

                <tr>
                <td>Palette bois</td>
                <td>Vente</td>
                <td>Validée</td>
                <td>10/03/2026</td>
                </tr>

                </tbody>

                </table>

                <!-- Notifications -->

                <h4 class="mt-5">Notifications</h4>

                <div class="notification-box shadow">
                    <p>Votre annonce "Chaise en bois" est en attente de validation.</p>
                    <p>Nouvelle formation disponible : Création de meubles en palette.</p>
                </div>

           

            </div>

        </div>

    </div>
    
    <?php include("footer_parts.php"); ?>

</body>
</html>