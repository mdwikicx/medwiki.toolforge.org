<?php

if ((empty($_GET) && empty($_POST)) || (count($_GET) == 1 && isset($_GET["test"]))) {
    // require_once __DIR__ . "/revisions.html";
    header("Location: revisions.html");
} else {
    require_once __DIR__ . "/main.php";
}
