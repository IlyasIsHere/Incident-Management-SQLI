<?php
$viewsPath2 = '../views/';
require '../controllers/accessController.php';

$userID = $_SESSION['id'];
$role = $_SESSION['role'];

// Generate JWT
require_once '../../vendor/autoload.php';
use Firebase\JWT\JWT;
$key = getenv("MERCURE_SUBSCRIBER_JWT_KEY");

$headers = [
    "alg" => "HS256",
    "typ" => "JWT"
];

$payload = [
    'mercure' => [
        'subscribe' => ["https://chat.com/incidents/$incidentID"]
    ]
];

$jwt = JWT::encode($payload, $key, 'HS256', null, $headers);
setcookie('mercureAuthorization', $jwt, path: '/.well-known/mercure');
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Détails de l'incident</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/backgroundStyle.css">
    <style>
        textarea {
            resize: none;
        }

        .list-group-item {
            background-color: #c9b0ff;
            transition: 200ms;
            border-color: #b69aff;
        }

        .list-group-item:hover {
            background-color: #b182ff;
            transition: 300ms;
        }
    </style>
</head>
<body>
    <?php
    if ($role == 'collaborateur')
        include $_SERVER["DOCUMENT_ROOT"] . '/src/views/Collaborateur/navbarCollaborateur.php';
    elseif ($role == 'technicien')
        include $_SERVER["DOCUMENT_ROOT"] . '/src/views/Technicien/navbarTechnicien.php';

    ?>



    <div class="container-fluid mt-5 d-flex flex-wrap column-gap-3 row-gap-3 justify-content-center pb-5">
        <div class="card col-12 col-sm-6 shadow">
            <h4 class="card-header ps-4">Détails de l'incident</h4>
            <div class="card-body ps-4 pe-4 fs-5">
                <p><strong>Titre: </strong><?php echo $titre; ?></p>
                <p><strong>Date: </strong><?php echo $date; ?></p>
                <p><strong>Description: </strong><?php echo $description; ?></p>
                <p><strong>Statut: </strong><?php echo $statut; ?></p>
                <p><strong>Type: </strong><?php echo $type; ?></p>
                <p><strong>Créateur: </strong><?php echo $collaborateurName; ?></p>
                <p><strong>Technicien associé: </strong><?php echo $technicienName; ?></p>
                <?php
                $directory = '../uploads/' . $incidentID;
                if (is_dir($directory)) {
                    echo "<p><strong>Pièces jointes: </strong></p>";
                    $files = glob($directory . '/*');
                    echo "<div class='list-group shadow'>";
                    foreach ($files as $file) {
                        $filename = basename($file);
                        echo "<a class='list-group-item list-group-item-action' href='$file' download>$filename</a>";
                    }
                    echo "</div>";
                }

                ?>
            </div>
        </div>

        <div class="card col-12 col-sm-5 shadow">
            <h4 class="card-header">Discussion</h4>
            <?php
            if ($canViewDiscussion) {
                ?>
            <div class="card-body overflow-y-auto" id="chatbox" style="height: 60vh;">
                <?php
                if ($role == 'collaborateur') {


                    foreach ($messages as $message) {
                        $msg_body = $message->getBody();

                        if ($message->getSender() == 'collaborateur') {
                            echo "<div class='d-flex justify-content-end'><div class='card shadow p-2 mb-1 bg-gradient bg-primary text-white'>$msg_body</div></div>";
                        }
                        elseif ($message->getSender() == 'technicien') {
                            echo "<div class='d-flex justify-content-start'><div class='card shadow p-2 mb-1 bg-gradient bg-light'>$msg_body</div></div>";
                        }
                    }
                }
                elseif ($role == 'technicien') {
                    foreach ($messages as $message) {
                        $msg_body = $message->getBody();
                        if ($message->getSender() == 'technicien') {
                            echo "<div class='d-flex justify-content-end'><div class='card shadow p-2 mb-1 bg-gradient bg-primary text-white'>$msg_body</div></div>";
                        }
                        elseif ($message->getSender() == 'collaborateur') {
                            echo "<div class='d-flex justify-content-start'><div class='card shadow p-2 mb-1 bg-gradient bg-light'>$msg_body</div></div>";
                        }
                    }
                }

                ?>
            </div>
            <div class="card-footer">
                <form method="POST" action="" id="chatForm">
                    <div class="d-flex flex-row gap-1">
                        <textarea class="form-control" placeholder="Ecrivez votre message ici" name="msgBody" id="msgInput" required></textarea>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Envoyer</button>
                    </div>
                </form>
            </div>
            <?php }
            else {
                echo "<div class='card-body'>Discussion non disponible.</div>";
            }
            ?>

        </div>

    </div>


    <?php if ($canViewDiscussion) { ?>

        <script>

            const $chatbox = $("#chatbox");
            const $msgInput = $("#msgInput");

            function scrollChatboxToBottom() {
                $chatbox.animate({scrollTop: $chatbox[0].scrollHeight}, 1000);
            }

            scrollChatboxToBottom();

            $msgInput.focus();

            $msgInput.keypress(function (e) {
                if (e.which === 13 && !e.shiftKey) {

                    e.preventDefault();
                    $("#submitBtn").click();
                }
            });

            $("#chatForm").on("submit", function(event) {
                event.preventDefault();

                $.post( "IncidentController.php?action=sendmsg&id=<?= $incidentID ?>", $("#chatForm").serialize() )
                    .done(function() {

                        let sentMsg = $msgInput.val();
                        let sentMsgDiv = "<div class='d-flex justify-content-end'><div class='card shadow p-2 mb-1 bg-gradient bg-primary text-white'>" + sentMsg + "</div></div>";
                        document.getElementById("chatbox").innerHTML += sentMsgDiv;

                        scrollChatboxToBottom();
                        $msgInput.val("");

                    })

            });

            const incidentID = <?= $incidentID ?>;
            const role = <?= json_encode($role) ?>;

            const url = new URL('http://localhost:3000/.well-known/mercure');
            url.searchParams.append('topic', 'https://chat.com/incidents/' + incidentID);

            const eventSource = new EventSource(url, {withCredentials: true});

            eventSource.onmessage = (e) => {
                const data = JSON.parse(e.data);
                const msg_sender = data.sender
                const msg_body = data.body;

                let msg_div;
                if (msg_sender != role) {
                    msg_div = "<div class='d-flex justify-content-start'><div class='card shadow p-2 mb-1 bg-gradient bg-light'>" + msg_body + "</div></div>";
                    document.getElementById("chatbox").innerHTML += msg_div;
                    scrollChatboxToBottom();
                }
            };

        </script>

    <?php } ?>

</body>
</html>
