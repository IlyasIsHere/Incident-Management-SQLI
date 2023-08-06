<?php

if (!isset($_SESSION['id'])) {
    header("Location: $viewsPath2" ."login.php");
    exit();
}