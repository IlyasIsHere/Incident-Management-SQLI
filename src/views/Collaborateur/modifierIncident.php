<?php
$viewsPath2 = '../';
require '../../controllers/IncidentController.php';

require '../../controllers/accessControllerCollaborateur.php';

$userID = $_SESSION["id"];

if (isset($_GET["id"])) {
    $incidentID = $_GET['id'];
    $controller = new IncidentController($incidentID);

    if (! ($userID == $controller->incidentModel->getIdCollaborateur())) {
        header('Location: ../error.php?error=4');
        exit();
    }

    $titre = $controller->incidentModel->getTitre();
    $type = $controller->incidentModel->getType();
    $date = $controller->incidentModel->getDate();
    $description = $controller->incidentModel->getDescription();

}
else {
    header('Location: ../error.php?error=3');
    exit();
}


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Modifier les détails de l'incident</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../css/backgroundStyle.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

</head>
<body>
    <?php include 'navbarCollaborateur.php'; ?>

    <div class="container-fluid mt-5 ps-sm-5 pe-sm-5">
        <div class="card shadow-lg col-sm-10 col-lg-6 mx-auto">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" action="../../controllers/IncidentController.php?action=edit&id=<?php echo $incidentID; ?>">
                    <div class="form-floating mb-2">
                        <select class="form-select" id="selectType" name="type">
                            <option value="Réseau" <?php echo $type == 'Réseau' ? 'selected' : ''; ?>>Réseau</option>
                            <option value="Electricité" <?php echo $type == 'Electricité' ? 'selected' : ''; ?>>Electricité</option>
                            <option value="Hardware" <?php echo $type == 'Hardware' ? 'selected' : ''; ?>>Hardware</option>
                            <option value="Software" <?php echo $type == 'Software' ? 'selected' : ''; ?>>Software</option>
                        </select>
                        <label for="selectType">Sélectionner le type de l'incident</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input required type="text" class="form-control" id="titre" placeholder="Titre de l'incident" name="titre" value="<?php echo $titre; ?>" >
                        <label for="titre">Titre</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input required type="datetime-local" class="form-control" id="date" name="date" <?php echo "value='$date'"; ?>>
                        <label for="date">Date de l'incident</label>
                    </div>
                    <div class="form-floating mb-2">
                        <textarea required class="form-control" id="description" name="description" placeholder="Donner plus de détails sur l'incident"><?php echo $description; ?></textarea>
                        <label for="description">Description</label>
                    </div>
                    <div class="mb-2">
                        <label for="formFileMultiple" class="ps-1 pb-1">Ajouter des pièces jointes</label>
                        <div class="input-group">
                            <input type="file" id="fileInput" class="form-control" name="file[]" multiple>
                            <button class="btn btn-secondary" type="button" id="resetButton">Supprimer</button>
                        </div>
                    </div>
                    <?php
                    $directory = "../../uploads/$incidentID";
                    if (is_dir($directory)) {
                        echo "<div>";

                        echo "<div class='d-inline-flex flex-column gap-1'>";
                        echo "<label>Supprimer des fichiers existants</label>";

                        $files = glob($directory . '/*');
                        for ($i = 0; $i < count($files); $i++) {
                            $filenameWithDir = $files[$i];
                            $filePathRelativeToController = substr($filenameWithDir, 3);
                            $filename = basename($files[$i]);
//                            echo "<div>";
                            echo "<input type='checkbox' class='btn-check' id='file$i' name='filesToDelete[]' value='$filePathRelativeToController'>";
                            echo "<label for='file$i' class='btn btn-outline-danger w-100'>$filename</label>";
//                            echo "</div>";
                        }

                        echo "</div>";
                        echo "</div>";

                    }

                    if (isset($_GET['error'])) {
                        if ($_GET['error'] == ERR_MAX_FILESIZE_EXCEEDED) {
                            echo "<div class='alert alert-danger pt-0 pb-0 text-wrap'>La taille de chaque fichier ne doit pas dépasser 2MB, et la taille totale ne doit pas dépasser 8 MB.</div>";
                        }
                        elseif ($_GET['error'] == ERR_UPLOAD_GENERAL) {
                            echo "<div class='alert alert-danger pt-0 pb-0'>Erreur lors de l'upload de votre fichier.</div>";
                        }
                    }
                    ?>

                    <button type="submit" class="btn btn-primary col-sm-4 float-end">Modifier</button>


                </form>
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