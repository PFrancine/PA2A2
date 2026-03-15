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
                <?php include("sidebar_parts.php"); ?>

            <!-- Contenu -->

            <div class="col-md-10 p-5 d-flex flex-column align-items-center">
                <?php
                    $prenom = $_SESSION['prenom'];
                ?>
                <h1 class="hello-user text-center">
                    Alors <?php echo $prenom; ?> quel objet vas-tu transformer en trésor aujourd’hui ?? ✨
                </h1>

                <h2 class="dashboard-title">Déposer une annonce</h2>

                <div class="form-container shadow-lg mt-4 p-4 bg-white rounded">

                    <form action="traitement_deposer_annonce.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Titre de l'objet</label>
                                <input type="text" name="titre" class="form-control form-control-lg" placeholder="Ex : Chaise en bois" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type d'annonce</label>
                                <select name="type_annonce" id="type_annonce" class="form-select form-select-lg">
                                    <option value="DON">Don</option>
                                    <option value="VENTE">Vente</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-4">

                            <label class="form-label">Catégorie de l'objet</label>
                            <div class="row g-3">

                                <div class="col-md-3">
                                    <label class="categorie-card">
                                        <input type="radio" name="categorie" value="meuble" required>
                                        <i class="fa-solid fa-chair"></i>
                                        <p>Meubles</p>
                                    </label>
                                </div>

                                <div class="col-md-3">
                                    <label class="categorie-card">
                                        <input type="radio" name="categorie" value="bois">
                                        <i class="fa-solid fa-tree"></i>
                                        <p>Bois</p>
                                    </label>
                                </div>

                                <div class="col-md-3">
                                    <label class="categorie-card">
                                        <input type="radio" name="categorie" value="electronique">
                                        <i class="fa-solid fa-laptop"></i>
                                        <p>Électronique</p>
                                    </label>
                                </div>

                                <div class="col-md-3">
                                    <label class="categorie-card">
                                        <input type="radio" name="categorie" value="vetement">
                                        <i class="fa-solid fa-shirt"></i>
                                        <p>Vêtements</p>
                                    </label>
                                </div>

                                <div class="col-md-3">
                                    <label class="categorie-card">
                                        <input type="radio" name="categorie" value="metal">
                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                        <p>Métal</p>
                                    </label>
                                </div>

                                <div class="col-md-3">
                                    <label class="categorie-card">
                                        <input type="radio" name="categorie" value="plastique">
                                        <i class="fa-solid fa-bottle-water"></i>
                                        <p>Plastique</p>
                                    </label>
                                </div>

                                <div class="col-md-3">
                                    <label class="categorie-card">
                                        <input type="radio" name="categorie" value="decoration">
                                        <i class="fa-solid fa-image"></i>
                                        <p>Décoration</p>
                                    </label>
                                </div>

                                <div class="col-md-3">
                                    <label class="categorie-card">
                                        <input type="radio" name="categorie" value="autre" id="categorie_autre">
                                        <i class="fa-solid fa-plus"></i>
                                        <p>Autre</p>
                                    </label>
                                </div>

                            </div>

                        </div>
                        <div class="mb-3" id="autre_categorie_container" style="display:none;">
                            <label class="form-label">Précisez la catégorie</label>
                            <input type="text" name="autre_categorie" class="form-control" placeholder="Ex : verre, carton...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Décrivez l'objet..." required></textarea>
                        </div>

                        <div class="mb-3" id="prix_container">
                            <label class="form-label">Prix (€)</label>
                            <input type="number" name="prix" step="0.01" class="form-control" placeholder="Ex : 20">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Photos de l'objet (max 5)</label>
                            <div class="upload-box" id="uploadBox">
                                <i class="fa-solid fa-camera"></i>
                                <p>Ajouter des photos</p>
                                <input type="file" name="images[]" id="imageInput" multiple accept="image/*" hidden>
                                </div>
                            <div id="preview" class="preview-container"></div>
                        </div>

                        <div class="text-end">
                            <button class="btn btn-success btn-lg px-4" name="publier">
                            Publier l'annonce
                            </button>
                        </div>

                    </form>
                </div>

            </div>

    
            <script>

                const typeAnnonce = document.getElementById("type_annonce");
                const prixContainer = document.getElementById("prix_container");

                function togglePrix(){
                if(typeAnnonce.value === "DON"){
                prixContainer.style.display = "none";
                }else{
                prixContainer.style.display = "block";
                }
                }

                typeAnnonce.addEventListener("change", togglePrix);

                togglePrix();
                

                const radiosCategorie = document.querySelectorAll("input[name='categorie']");
                const autreContainer = document.getElementById("autre_categorie_container");

                radiosCategorie.forEach(radio => {
                radio.addEventListener("change", function(){

                if(this.value === "autre"){
                autreContainer.style.display = "block";
                }else{
                autreContainer.style.display = "none";
                }

                });
                });

                const uploadBox = document.getElementById("uploadBox");
                const imageInput = document.getElementById("imageInput");
                const preview = document.getElementById("preview");

                let selectedFiles = [];

                /* ouvrir l'explorateur */
                uploadBox.addEventListener("click", () => {
                imageInput.click();
                });

                /* sélection fichiers */
                imageInput.addEventListener("change", function(e){
                let files = Array.from(e.target.files);
                if(selectedFiles.length + files.length > 5){
                alert("Vous pouvez ajouter maximum 5 images");
                return;
                }

                files.forEach(file => selectedFiles.push(file));

                displayImages();

                });

                /* affichage preview */

                function displayImages(){
                    preview.innerHTML = "";
                    selectedFiles.forEach((file,index)=>{
                    let reader = new FileReader();

                    reader.onload = function(e){
                    let div = document.createElement("div");
                    div.classList.add("preview-image");
                    div.innerHTML = `
                    <img src="${e.target.result}">
                    <button type="button" class="remove-btn" onclick="removeImage(${index})">×</button>
                    `;
                    preview.appendChild(div);
                    }

                    reader.readAsDataURL(file);
                    });

                    /* IMPORTANT : remettre les fichiers dans l'input */
                    const dataTransfer = new DataTransfer();
                    selectedFiles.forEach(file => dataTransfer.items.add(file));
                    imageInput.files = dataTransfer.files;

                }

                /* supprimer image */
                function removeImage(index){
                selectedFiles.splice(index,1);

                displayImages();

                }

            </script>
    
        </div>    <?php include("footer_parts.php"); ?>
    </div>

</body>
</html>