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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Espace Particulier - UpcycleConnect</title>
<?php include("header_parts.php"); ?>


</head>

<body>

    <div class="container-fluid">
        <div class="row">

            
                <?php include("sidebar_parts.php"); ?>
            
            <div class="col-md-10 p-4">
                <?php
                    $prenom = $_SESSION['prenom'];
                ?>
                <h2 class="dashboard-title">Heyy <?php echo $prenom; ?> 👋 <br>
                 Bienvenue sur ton espace particulier  </h2>
                <p>Retrouvez ici vos activités et vos actions rapides.</p>

                <!-- Actions rapides -->

                <div class="row mt-4">

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                        <div class="card card-action p-3 text-center shadow">
                            <h5>Déposer un objet</h5>
                            <p>Publier une annonce</p>
                            <a href="deposer_annonce.php" class="btn btn-success">Créer</a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                        <div class="card card-action p-3 text-center shadow">
                            <h5>Formations</h5>
                            <p>Voir les ateliers</p>
                            <button class="btn btn-success">Explorer</button>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                        <div class="card card-action p-3 text-center shadow">
                            <h5>Mon planning</h5>
                            <p>Voir mes activités</p>
                            <button class="btn btn-success">Consulter</button>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                        <div class="card card-action p-3 text-center shadow">
                            <h5>Upcycling Score</h5>
                            <p>Voir mon impact</p>
                            <button class="btn btn-success">Voir</button>
                        </div>
                    </div>
                </div>

                

                <h4 class="mt-5">Mes dernières annonces</h4>
                <?php

                    $id_utilisateur = $_SESSION['id_utilisateur'];

                    $sql = "SELECT titre, type_annonce, statut, date_publication
                            FROM annonce
                            WHERE id_utilisateur = ?
                            ORDER BY date_publication DESC
                            LIMIT 3";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$id_utilisateur]);

                    $annonces = $stmt->fetchAll();

                ?>
                <div class="table-responsive table-container">
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

                            <?php if(count($annonces) > 0): ?>
                            <?php foreach($annonces as $annonce): ?>

                            <tr>
                                <td><?php echo htmlspecialchars($annonce['titre']); ?></td>

                                <td><?php echo htmlspecialchars($annonce['type_annonce']); ?></td>

                                <td><?php echo htmlspecialchars($annonce['statut']); ?></td>

                                <td><?php echo date("d/m/Y", strtotime($annonce['date_publication'])); ?></td>
                            </tr>

                            <?php endforeach; ?>
                            <?php else: ?>

                            <tr>
                            <td colspan="4" class="text-center">
                            Aucune annonce publiée pour le moment
                            </td>
                            </tr>

                            <?php endif; ?>


                        </tbody>

                    </table>
                </div>
                <div class="text-end">
                    <a href="mes_annonces.php" class="btn btn-sm annonces-btn">
                    Voir toutes mes annonces
                    </a>
                </div>
        

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