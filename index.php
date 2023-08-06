<?php
session_start();
if (isset($_SESSION["id"])) {

    if ($_SESSION["role"] == 'collaborateur') {
        header("Location: src/views/Collaborateur/AccueilCollaborateur.php");
    }
    elseif ($_SESSION["role"] == 'technicien') {
        header("Location: src/views/Technicien/AccueilTechnicien.php");
    }
}
else {
    header("Location: src/views/login.php");
}
exit();