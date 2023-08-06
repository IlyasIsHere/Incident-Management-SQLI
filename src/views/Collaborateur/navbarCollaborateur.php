<?php
if (! (isset($viewsPath) && isset($controllersPath))) {
    $viewsPath = '../';
    $controllersPath = '../../controllers/';
}
?>

<nav class="navbar navbar-expand-lg bg-light sticky-top shadow-lg">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?php echo $viewsPath . 'Collaborateur/AccueilCollaborateur.php'; ?>">Gestion des incidents SQLI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="<?php echo $viewsPath . 'Collaborateur/AccueilCollaborateur.php'; ?>">Accueil</a>
                <a class="nav-link" href="<?php echo $viewsPath . 'Collaborateur/creerIncident.php'; ?>">Créer un ticket</a>
                <a class="nav-link" href="#">À propos</a>
            </div>
        </div>
        <div class="pe-3">Bienvenue <strong><?php echo $_SESSION['prenom'] .' '. $_SESSION['nom']; ?></strong></div>
        <div>
            <a href="<?php echo $controllersPath . 'LoginController.php?action=logout'; ?>" class="btn btn-outline-danger">Se déconnecter</a>
        </div>
    </div>


</nav>