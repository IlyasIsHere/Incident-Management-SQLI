<?php

if (!isset($_SESSION['id'])) {
    header("Location: $viewsPath2" ."login.php");
    exit();
}
elseif (isset($_SESSION["role"]) && $_SESSION["role"] == 'collaborateur') {
    header("Location: $viewsPath2" ."error.php?error=4");
    exit();
}
