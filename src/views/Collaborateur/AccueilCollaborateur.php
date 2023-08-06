<?php
session_start();
$viewsPath2 = '../';
require '../../controllers/accessControllerCollaborateur.php';


$userID = $_SESSION['id'];
require_once '../../models/Incident.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Accueil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../css/backgroundStyle.css">
    <script src="https://kit.fontawesome.com/b72eda9c8f.js" crossorigin="anonymous"></script>




    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">

    <style>
        .fa-trash {
            margin-right: 3px;
        }

        .hover-up {
            transition: transform .3s ease-out;
        }

        .hover-up:hover {
            transform: scale(1.4);
        }
    </style>

</head>
<body>
    <?php include 'navbarCollaborateur.php'; ?>

    <div class="container-fluid mt-1 p-5">
        <div class="d-flex justify-content-center">
            <?php
            if (isset($_GET['msg'])) {
                if ($_GET['msg'] == 'delete')
                    echo "<div class='alert alert-danger alert-dismissible fade show'>Incident supprimé. <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                elseif ($_GET['msg'] == 'add')
                    echo "<div class='alert alert-success alert-dismissible fade show'>Ticket soumis avec succès. <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                elseif ($_GET['msg'] == 'edit') {
                    echo "<div class='alert alert-success alert-dismissible fade show'>Ticket modifié avec succès. <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                }
                elseif ($_GET['msg'] == 'confirm') {
                    echo "<div class='alert alert-success alert-dismissible fade show'>Succès, l'incident est réglé! <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                }
                elseif ($_GET['msg'] == 'decline') {
                    echo "<div class='alert alert-info alert-dismissible fade show'>Le ticket est revenu en état d'attente. <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                }
            }
            ?>
        </div>


        <div class="d-flex justify-content-end"><a href="creerIncident.php" class="btn btn-primary mb-2">Créer un nouveau ticket</a></div>

        <div class="card shadow-lg">
            <div class="card-body">
                <table class="table table-striped table-bordered table-hover" id="table">
                    <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $userIncidents = Incident::getUserIncidents($userID);
                    foreach ($userIncidents as $incident) {
                        $incidentID = $incident["id"];
                        echo '<tr>';
                        echo '<td>' . $incident['titre'] . '</td>';
                        echo '<td>' . $incident['type'] . '</td>';
                        echo '<td>' . $incident['date'] . '</td>';
                        echo '<td>' . $incident['statut'] . '</td>';
                        echo '<td class="">';

                        echo "<div class='d-flex justify-content-center gap-sm-3'>";
                        echo "<a href='../../controllers/IncidentController.php?action=delete&id=$incidentID' class='hover-up'><i class='fa-solid fa-trash fa-lg' style='color: #ff0000;'></i></a>";
                        echo "<a href='../../views/Collaborateur/modifierIncident.php?id=$incidentID' class='hover-up'><i class='fa-solid fa-pen-to-square fa-lg' style='color: #00ad00;'></i></a>";
                        echo "<a href='../../controllers/IncidentController.php?action=view&id=$incidentID' class='hover-up'><i class='fa-solid fa-eye fa-lg' style='color: #005eff;'></i></a>";

                        if ($incident['last_message_sender'] === 'technicien' && ! $incident['last_message_was_seen']) {
                            echo "<a class='position-absolute'><i class='fa-solid fa-circle fa-xs position-absolute' style='color: #5b30d2; text-shadow: rgba(0,0,255,0.4) 0px 0px 10px; right: 75px; top: 15px; filter: blur(2px);'></i></a>";
                        }

                        echo "</div>";

                        echo "<div class='d-flex justify-content-center gap-1 mt-2'>";
                        if ($incident['statut'] == 'Traité') {
                            echo "<a href='../../controllers/IncidentController.php?action=confirm&id=$incidentID' class='btn btn-success'>Le problème est réglé</a>";
                            echo "<a href='../../controllers/IncidentController.php?action=decline&id=$incidentID' class='btn btn-danger'>Le problème persiste</a>";
                        }
                        echo "</div>";

                        echo '</td>';


                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $('#table').DataTable( {
            fixedHeader: true
        } );

        $("#table").parent().addClass("table-responsive");
    </script>

</body>
</html>
