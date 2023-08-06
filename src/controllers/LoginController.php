<?php
session_start();

require_once '../models/Utilisateur.php';

class LoginController {
    protected $userModel;
    function __construct($email, $password) {
        $this->userModel = new Utilisateur($email, $password);
    }

    function getUserModel() {
        return $this->userModel;
    }

    public function login() {
        $userModel = $this->getUserModel();

        if ($userModel->checkLogin()) {
            $user_info = $userModel->getUserRow();

            $_SESSION['email'] = $user_info['email'];
            $_SESSION['id'] = $user_info['id'];
            $_SESSION['nom'] = ucfirst(strtolower($user_info['nom']));
            $_SESSION['prenom'] = ucfirst(strtolower($user_info['prenom']));
            $_SESSION['role'] = $user_info['role'];

            if ($user_info['role'] == 'collaborateur') {
                header("Location: ../views/Collaborateur/AccueilCollaborateur.php");
            }
            elseif ($user_info['role'] == 'technicien') {
                header("Location: ../views/Technicien/AccueilTechnicien.php");
            }
            exit();
        }
        else {
            header('Location: ../views/login.php?error=1'); // Error #1 means either email or password is incorrect
            exit();
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ../views/login.php');
        exit();
    }
}

if (isset($_GET['action'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_GET['action'] == 'login') {
        setcookie("emailInputValue", $_POST["email"], 0, '/');

        $loginController = new LoginController($_POST['email'], $_POST['password']);
        $loginController->login();
    }
    elseif ($_GET['action'] == 'logout') {
        $loginController = new LoginController(null, null);
        $loginController->logout();
    }
}
