<?php
session_start();
$viewsPath2 = '../';
require '../../controllers/accessControllerTechnicien.php';


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
    <!--    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-1.13.5/date-1.5.1/fh-3.4.0/r-2.5.0/datatables.min.css" rel="stylesheet"/>-->
    <!---->
    <!--    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-1.13.5/date-1.5.1/fh-3.4.0/r-2.5.0/datatables.min.js"></script>-->




    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">

    <style>
        td a:first-child {
            width: 70px;
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
    <?php include 'navbarTechnicien.php'; ?>

    <div class="container-fluid mt-1 p-5">
        <div class="d-flex justify-content-center">
            <?php
            if (isset($_GET['msg'])) {
                if ($_GET['msg'] == 'associe')
                    echo "<div class='alert alert-primary alert-dismissible fade show'>Vous êtes désormais responsable de régler cet incident. <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                elseif ($_GET['msg'] == 'fini')
                    echo "<div class='alert alert-success alert-dismissible fade show'>Succès! Le créateur du ticket confirmera si l'incident est réglé. <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";

            }
            ?>
        </div>

        <div class="card shadow-lg">
            <div class="card-body">
                <table class="table table-striped table-bordered table-hover" id="table">
                    <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th class="last-column text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($_GET['data']) && $_GET['data'] == 'some') {
                        $incidents = Incident::getAssociatedIncidents($userID);
                    }
                    elseif (isset($_GET['data']) && $_GET['data'] == 'all') {
                        $incidents = Incident::getAllIncidents();
                    }
                    elseif (!isset($_GET['data'])) {
                        $incidents = Incident::getAllIncidents();
                    }

                    foreach ($incidents as $incident) {
                        $incidentID = $incident["id"];
                        echo '<tr>';
                        echo '<td>' . $incident['titre'] . '</td>';
                        echo '<td>' . $incident['type'] . '</td>';
                        echo '<td>' . $incident['date'] . '</td>';
                        echo '<td>' . $incident['statut'] . '</td>';
                        echo '<td class="last-column d-flex justify-content-evenly flex-wrap">';
                        if ($incident['statut'] == 'En attente') {
                            echo "<a href='../../controllers/IncidentController.php?action=traiter&id=$incidentID' class='btn btn-primary'>Traiter</a>";
                        } elseif ($incident['statut'] == 'En cours' && $incident['id_technicien'] == $userID) {
                            echo "<a href='../../controllers/IncidentController.php?action=finir&id=$incidentID' class='btn btn-success'>Finir</a>";
                        }

                        echo "<a href='../../controllers/IncidentController.php?action=view&id=$incidentID' class='hover-up'><i class='fa-solid fa-eye fa-lg align-middle pt-2' style='color: #005eff;'></i></a>";

                        if ($incident['id_technicien'] == $userID) {
                            if ($incident['last_message_sender'] === 'collaborateur' && ! $incident['last_message_was_seen']) {
                                echo "<a class='position-absolute'><i class='fa-solid fa-circle fa-xs position-absolute' style='color: #ff0000; text-shadow: red 0px 0px 10px; right: 85px; top: 18px; filter: blur(2px);'></i></a>";
                            }
                        }

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
    </script>
</body>
</html>