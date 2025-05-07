<?php

if (empty($_GET) && empty($_POST)) {
    require_once __DIR__ . "/revisions_new.php";
} else {
    require_once __DIR__ . "/main.php";
}
