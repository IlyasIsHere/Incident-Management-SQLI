<?php
if (isset($_GET["error"])) {
    switch ($_GET["error"]) {
        case 2:
            echo "Error! Incident not found!";
            break;

        case 3:
            echo 'Some error occurred! Try again later.';
            break;

        case 4:
            echo 'Error! You are unauthorized to view this page!';
            break;
    }
}