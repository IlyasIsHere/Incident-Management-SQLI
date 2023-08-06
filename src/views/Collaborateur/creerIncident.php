<?php
session_start();
$viewsPath2 = '../';
require '../../controllers/accessControllerCollaborateur.php';

const ERR_MAX_FILESIZE_EXCEEDED = 1;
const ERR_UPLOAD_GENERAL = 2;
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../css/backgroundStyle.css">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

    <title>Créer un nouveau ticket</title>
    <style>
        textarea {
            height: 10em;
        }
    </style>

</head>
<body>
    <?php
    include 'navbarCollaborateur.php';

    ?>

    <div class="container-fluid mt-5 ps-5 pe-5">
        <div class="row">

            <div class="col-sm-5 ps-5 pt-5">
                <h1>NOUVEAU TICKET</h1>
            </div>

            <div class="col-sm-7 card shadow-lg">
                <div class="card-body">
                    <form method="POST" action="../../controllers/IncidentController.php?action=add" enctype="multipart/form-data">
                        <div class="form-floating mb-2">
                            <select class="form-select" id="selectType" name="type">
                                <option value="Réseau">Réseau</option>
                                <option value="Electricité">Electricité</option>
                                <option value="Hardware">Hardware</option>
                                <option value="Software">Software</option>
                            </select>
                            <label for="selectType">Sélectionner le type de l'incident</label>
                        </div>
                        <div class="mb-2">
                            <input required type="text" class="form-control" placeholder="Titre de l'incident" name="titre">
                        </div>
                        <div class="form-floating mb-2">
                            <input required type="datetime-local" class="form-control" id="date" name="date">
                            <label for="date">Date de l'incident</label>
                        </div>
                        <div class="mb-2">
                            <textarea required class="form-control" name="description" placeholder="Donner plus de détails sur l'incident"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="formFileMultiple">Pièces jointes</label>
                            <div class="input-group">
                                <input type="file" id="fileInput" class="form-control" name="file[]" multiple>
                                <button class="btn btn-secondary" type="button" id="resetButton">Supprimer</button>
                            </div>
                        </div>
                        <div>
                            <?php
                            if (isset($_GET['error'])) {
                                if ($_GET['error'] == ERR_MAX_FILESIZE_EXCEEDED) {
                                    echo "<span class='alert alert-danger pt-0 pb-0'>La taille de chaque fichier ne doit pas dépasser 2MB, et la taille totale ne doit pas dépasser 8 MB.</span>";
                                }
                                elseif ($_GET['error'] == ERR_UPLOAD_GENERAL) {
                                    echo "<span class='alert alert-danger pt-0 pb-0'>Erreur lors de l'upload de votre fichier.</span>";
                                }
                            }
                            ?>
                            <button type="submit" class="btn btn-primary col-4 float-end">Envoyer</button>
                        </div>


                    </form>
                </div>
            </div>

        </div>


    </div>


    <script>
        $("#resetButton").click(function() {
            $("#fileInput").val("");
        });
    </script>
</body>
</html>