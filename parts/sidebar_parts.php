<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<div class="col-md-2 sidebar">

    <div class="logo text-center mb-4">
        <img src="../images/logo_upcycle.png" alt="Logo Upcycle">
    </div>

    <a href="dashboard_parts.php" class="<?= $page == 'dashboard_parts.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>

    <a href="deposer_annonce.php" class="<?= $page == 'deposer_annonce.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-plus"></i> Déposer une annonce
    </a>

    <a href="#" class="<?= $page == 'conteneur.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-box"></i> Dépôt conteneur
    </a>

    <a href="#">
        <i class="fa-solid fa-calendar"></i> Formations / Événements
    </a>

    <a href="#">
        <i class="fa-solid fa-clock"></i> Mon planning
    </a>

    <a href="#">
        <i class="fa-solid fa-lightbulb"></i> Conseils
    </a>

    <a href="#">
        <i class="fa-solid fa-leaf"></i> Upcycling Score
    </a>

    <a href="#">
        <i class="fa-solid fa-bell"></i> Notifications
    </a>

    <a href="#">
        <i class="fa-solid fa-user"></i> Mon profil
    </a>

    <a href="../auth/deconnexion.php" class="logout-link">
        <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
    </a>

</div>