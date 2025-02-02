<?php

if (isset($_GET['test'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}
function test_print($str)
{
    if (isset($_GET['test'])) {
        echo "\n<br>\n";
        echo $str;
        echo "\n<br>\n";
    }
}
include_once __DIR__ . '/WikiText/require.php';

require_once __DIR__ . "/post.php";
require_once __DIR__ . "/fix_html.php";
require_once __DIR__ . "/html_to_Segments.php";
require_once __DIR__ . "/wikitext_to_html.php";
require_once __DIR__ . "/WikiText/lead_section.php";
require_once __DIR__ . "/WikiText/index.php";
