<?php
global $db;
session_start();
$userID = $_SESSION["id"];
$role = $_SESSION["role"];

require_once $_SERVER["DOCUMENT_ROOT"] . '/src/models/Incident.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/src/models/Utilisateur.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/src/models/Message.php';

use Firebase\JWT\JWT;


const ERR_MAX_FILESIZE_EXCEEDED = 1;
const FILES_EMPTY = 2;



class IncidentController {
    public $incidentModel;

    public function __construct($id=null) {
        if (!is_null($id)) {
            $this->incidentModel = new Incident($id);
            $this->checkIncidentExists();
        } else {
            $this->incidentModel = new Incident();
        }
    }

    public function checkIncidentExists() {
        global $viewsPath2;

        if ($this->incidentModel->setIncidentInfo())
            return true;
        else {
            if (isset($viewsPath2))
                header("Location: $viewsPath2" . "error.php?error=2");
            else
                header('Location: ../views/error.php?error=2');
            exit();
        }
    }

    public function deleteIncident() {
        if ($this->incidentModel->deleteIncident())
            header('Location: ../views/Collaborateur/AccueilCollaborateur.php?msg=delete');
        else
            header('Location: ../views/error.php?error=3');

        exit();
    }

    public function editIncident($titre, $description, $type, $date) {
        if ($this->incidentModel->editIncident($titre, $description, $type, $date))
            header('Location: ../views/Collaborateur/AccueilCollaborateur.php?msg=edit');
        else
            header('Location: ../views/error.php?error=3');

        exit();
    }

    public function viewIncident() {
        global $userID;
        global $role;
        $incidentID = $this->incidentModel->getID();

        if ($userID == $this->incidentModel->getIdCollaborateur() || $role == 'technicien') {
            $type = $this->incidentModel->getType();
            $titre = $this->incidentModel->getTitre();
            $statut = $this->incidentModel->getStatut();
            $description = $this->incidentModel->getDescription();
            $date = $this->incidentModel->getDate();
            $technicienID = $this->incidentModel->getIdTechnicien();
            $collaborateurID = $this->incidentModel->getIdCollaborateur();

            $technicienRow = Utilisateur::getUserRowById($technicienID);
            $technicienName = $technicienRow ? ($technicienRow['prenom'] . ' ' . $technicienRow['nom']) : 'Incident pas encore traité.';

            $collaborateurRow = Utilisateur::getUserRowById($collaborateurID);
            $collaborateurName = $collaborateurRow['prenom'] . ' ' . $collaborateurRow['nom'];

            $last_message_sender = $this->incidentModel->getLastMessageSender();
            $last_message_was_seen = $this->incidentModel->getLastMessageWasSeen();

            $messages = Message::getAllMsgsByIncidentID($incidentID);

            $canViewDiscussion = (($role == 'technicien') && ($technicienID == $userID)) || ($role == 'collaborateur');

            if ($canViewDiscussion) {
                if ($last_message_sender !== $role && ! $last_message_was_seen) {
                    $this->incidentModel->declareLastMessageAsSeen();
                }
            }


            $viewsPath = '../views/';
            $controllersPath = '';

            require '../views/voirIncident.php';
        }
        else {
            header("Location: ../views/error.php?error=4");
            exit();
        }
    }

    public function associerTechnicien()
    {
        global $userID;
        global $role;
        if ($role == 'technicien' && $this->incidentModel->getStatut() == 'En attente') {
            if ($this->incidentModel->associerTechnicien($userID)) {
                header('Location: ../views/Technicien/AccueilTechnicien.php?msg=associe');
            }
            else {
                header('Location: ../views/error.php?error=3');
            }
        }
        else {
            header('Location: ../views/error.php?error=3');
        }
        exit();
    }

    public function finirIncident()
    {
        global $userID;
        global $role;
        if ($role == 'technicien' && $this->incidentModel->getStatut() == 'En cours' && $this->incidentModel->getIdTechnicien() == $userID && $this->incidentModel->finirIncident()) {
            header("Location: ../views/Technicien/AccueilTechnicien.php?msg=fini");
        }
        else {
            header("Location: ../views/error.php?error=3");
        }
        exit();
    }

    public function confirmIncidentSolved()
    {
        global $userID;
        global $role;

        if ($role == 'collaborateur' && $this->incidentModel->getStatut() == 'Traité' && $this->incidentModel->getIdCollaborateur() == $userID && $this->incidentModel->confirmIncidentSolved()) {
            header("Location: ../views/Collaborateur/AccueilCollaborateur.php?msg=confirm");
        }
        else {
            header('Location: ../views/error.php?error=4');
        }
        exit();
    }

    public function declineIncidentSolved()
    {
        global $userID;
        global $role;

        if ($role == 'collaborateur' && $this->incidentModel->getStatut() == 'Traité' && $this->incidentModel->getIdCollaborateur() == $userID && $this->incidentModel->declineIncidentSolved()) {
            header("Location: ../views/Collaborateur/AccueilCollaborateur.php?msg=decline");
        }
        else {
            header('Location: ../views/error.php?error=4');
        }
        exit();
    }

    public function sendMessage($body)
    {
        global $userID;
        global $role;

        $incidentID = $this->incidentModel->getID();

        $message = new Message($body, $incidentID, $role);
        if ($this->incidentModel->getIdCollaborateur() == $userID || $this->incidentModel->getIdTechnicien() == $userID) {
            try {
                if ($message->writeMsgToDB() && $this->incidentModel->updateLastMessageSender($role)) {

//                    header("Location: IncidentController.php?action=view&id=$incidentID");

                    $data['body'] = $message->getBody();
                    $data['sender'] = $message->getSender();

                    $this->mercurePublishPrivateMessage($data);

                }
                else
                    throw new Exception();
            }
            catch(Exception $e) {
                header('Location: ../views/error.php?error=3');
            }
        }
        else
            header('Location: ../views/error.php?error=4');

        exit();
    }

    public function validateFiles(): int
    {
        if (empty($_FILES)) { // This means that the uploaded files exceed the post_max_size
            return ERR_MAX_FILESIZE_EXCEEDED;
        }
        else {
            $num_files = count($_FILES["file"]["name"]);

            if ($_FILES["file"]["name"][0] != '') { // check if any files were uploaded

                for ($i = 0; $i < $num_files; $i++) {
                    $file_size = $_FILES["file"]["size"][$i];

                    if ($file_size == 0) { // means that one of the uploaded files exceed the upload_max_size
                        return ERR_MAX_FILESIZE_EXCEEDED;
                    }
                }

                return 0;
            }
            else {
                return FILES_EMPTY;
            }
        }


    }

    public function uploadIncidentFiles($incidentID): void
    {
        $directory = "../uploads/$incidentID";
        $num_files = count($_FILES["file"]["name"]);

        if (!is_dir($directory)) {
            mkdir($directory);
            $newDirectory = true;
        }
        else {
            $newDirectory = false;
        }

        for ($i = 0; $i < $num_files; $i++) {
            $file_name = basename($_FILES["file"]["name"][$i]);
            $file_tmp_name = $_FILES["file"]["tmp_name"][$i];

            if (! $newDirectory) {
                $j = 1;
                $arr = explode(".", $file_name);
                $shortname = $arr[0];
                $ext = end($arr);

                while (file_exists($directory . "/$file_name")) {
                    $newShortname = $shortname . " ($j)";

                    $file_name = $newShortname . "." . $ext;
                    $j++;
                }
            }

            move_uploaded_file($file_tmp_name, $directory . "/$file_name");
        }
    }

    public function deleteIncidentDirectory($incidentID): void
    {
        $directory = "../uploads/$incidentID";
        $files = glob($directory . "/*");

        foreach ($files as $file)
            unlink($file);

        rmdir($directory);
    }

    public static function deleteFiles($files): void
    {
        foreach ($files as $file) {
            if (file_exists($file))
                unlink($file);
        }
    }

    public function mercurePublishPrivateMessage($data)
    {
        $incidentID = $this->incidentModel->getID();
        require_once '../../vendor/autoload.php';

        // Generating the JWT key

        $publisherKey = getenv("MERCURE_PUBLISHER_JWT_KEY");
        $topic = "https://chat.com/incidents/$incidentID";

        $headers = [
            "alg" => "HS256",
            "typ" => "JWT"
        ];

        $payload = [
            'mercure' => [
                'publish' => [$topic]
            ]
        ];

        $jwt = JWT::encode($payload, $publisherKey, 'HS256', null, $headers);


        // Publishing to mercure hub through POST request
        $postData = http_build_query([
            'topic' => $topic,
            'data' => json_encode($data),
            'private' => 'on'
        ]);

        echo file_get_contents(
            'http://mercure/.well-known/mercure',
            false,
            stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Bearer $jwt",
                    'content' => $postData
                ]
            ])
        );

    }


}

if (isset($_GET['action'])) {
    if (isset($_GET['id'])) {
        $incidentID = $_GET['id'];
        $controller = new IncidentController($incidentID);

        switch ($_GET['action']) {
            case 'delete':

                if (is_dir("../uploads/$incidentID")) {
                    $controller->deleteIncidentDirectory($incidentID);
                }

                $controller->deleteIncident();
                break;

            case 'edit':
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                    switch ($controller->validateFiles()) {
                        case ERR_MAX_FILESIZE_EXCEEDED:
                            header('Location: ../views/Collaborateur/modifierIncident.php?id=' . $incidentID . '&error=' . ERR_MAX_FILESIZE_EXCEEDED);
                            break;

                        case FILES_EMPTY:
                            if (!empty($_POST["filesToDelete"])) {
                                $controller::deleteFiles($_POST["filesToDelete"]);
                            }
                            $controller->editIncident($_POST["titre"], $_POST["description"], $_POST["type"], $_POST["date"]);
                            break;

                        case 0:
                            if (!empty($_POST["filesToDelete"])) {
                                $controller::deleteFiles($_POST["filesToDelete"]);
                            }
                            $controller->uploadIncidentFiles($incidentID);
                            $controller->editIncident($_POST["titre"], $_POST["description"], $_POST["type"], $_POST["date"]);
                            break;

                    }

                }
                else {
                    header('Location: ../views/error.php?error=3');
                }
                exit();

            case 'view':
                $controller->viewIncident();
                break;

            case 'sendmsg':
                if ($_SERVER["REQUEST_METHOD"] == 'POST') {
                    $controller->sendMessage($_POST['msgBody']);
                }
                else {
                    header('Location: ../views/error.php?error=3');
                    exit();
                }
                break;

            case 'traiter':
                $controller->associerTechnicien();
                break;

            case 'finir':
                $controller->finirIncident();
                break;

            case 'confirm':
                $controller->confirmIncidentSolved();
                break;

            case 'decline':
                $controller->declineIncidentSolved();
                break;
        }
    }
    elseif ($_GET['action'] == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {

        $type = $_POST["type"];
        $titre = $_POST["titre"];
        $date = $_POST["date"];
        $description = $_POST["description"];

        $controller = new IncidentController();

        if (Incident::addIncident($userID, $type, $titre, $date, $description)) {
            $incidentID = $db->lastInsertId();

            switch ($controller->validateFiles()) {
                case ERR_MAX_FILESIZE_EXCEEDED:
                    Incident::deleteIncidentByID($incidentID);
                    header('Location: ../views/Collaborateur/creerIncident.php?error=' . ERR_MAX_FILESIZE_EXCEEDED);
                    break;

                case FILES_EMPTY:
                    header('Location: ../views/Collaborateur/AccueilCollaborateur.php?msg=add');
                    break;

                case 0:
                    $controller->uploadIncidentFiles($incidentID);
                    header('Location: ../views/Collaborateur/AccueilCollaborateur.php?msg=add');
                    break;
            }

        }
        else {
            header('Location: ../views/error.php?error=3');
        }
        exit();

    }

}




