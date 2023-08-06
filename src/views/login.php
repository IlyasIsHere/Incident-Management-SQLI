<?php
session_start();
if (isset($_SESSION["id"])) {

    if ($_SESSION["role"] == 'collaborateur') {
        header("Location: Collaborateur/AccueilCollaborateur.php");
    }
    elseif ($_SESSION["role"] == 'technicien') {
        header("Location: Technicien/AccueilTechnicien.php");
    }
    exit();
}
?>

<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Se Connecter</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/backgroundStyle.css">

    <style>
        .card {
            background: #b3c4ff;  /* fallback for old browsers */
            background: -webkit-linear-gradient(to right, #dca0ff, #b3c4ff);  /* Chrome 10-25, Safari 5.1-6 */
            background: linear-gradient(to right, #dca0ff, #b3c4ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

        }
    </style>
</head>
<body class="h-100">
    <h1 class="text-center pt-5">Système de gestion d'incidents</h1>
    <div class="ps-5 pb-5 d-flex align-items-center h-75 flex-wrap">
        <div class="col-sm-6">
            <img src="../resources/SQLI_DX_LOGO_violet.png" alt="sqli-logo">
        </div>
        <div class="card col-sm-5 p-4 shadow">
            <div class="card-body">
                <h2 class="mb-4">Connexion</h2>
                <form method="POST" action="../controllers/LoginController.php?action=login">
                    <div class="form-floating mt-3" id="emailDiv">
                        <input type="email" required class="form-control <?php if (isset($_GET['error']) && $_GET['error'] == 1) echo 'is-invalid'; ?>" id="emailInput" placeholder="email" name="email" value="<?php if (isset($_COOKIE['emailInputValue'])) echo $_COOKIE['emailInputValue']; ?>">
                        <label for="emailInput">Email</label>
                    </div>
                    <div class="form-floating mt-3" id="passwordDiv">
                        <input type="password" required class="form-control <?php if (isset($_GET['error']) && $_GET['error'] == 1) echo 'is-invalid'; ?>" id="passwordInput" placeholder="password" name="password">
                        <label for="passwordInput">Mot de passe</label>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Se Connecter</button>
                    <?php if (isset($_GET['error']) && $_GET['error'] == 1) echo "<div class='alert alert-danger p-2 mt-2 mb-0' id='alertDiv'>Votre email ou mot de passe est incorrect.</div>"; ?>

                </form>
            </div>
        </div>
    </div>

    <script>
        const emailInput = document.getElementById("emailInput");
        const passwordInput = document.getElementById("passwordInput");

        emailInput.focus();
        var val = emailInput.value;
        emailInput.value = '';
        emailInput.value = val;


        function resetAppearance() {
            emailInput.classList.remove('is-invalid');
            passwordInput.classList.remove('is-invalid');
        }

        emailInput.addEventListener("input", resetAppearance);
        passwordInput.addEventListener("input", resetAppearance);

    </script>
</body>
</html>