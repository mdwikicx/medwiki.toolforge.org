<?php

if (isset($_GET['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

function test_print($str)
{
    if (isset($_GET['test']) || defined('DEBUGX')) {
        echo $str;
        echo "\n";
    }
}

include_once __DIR__ . '/src/WikiText/require.php';

require_once __DIR__ . "/src/file_helps.php";
require_once __DIR__ . "/jsons_data/json_data.php";
require_once __DIR__ . "/src/post.php";
require_once __DIR__ . "/src/post_mdwiki.php";
require_once __DIR__ . "/src/fix_html.php";
require_once __DIR__ . "/src/html_to_Segments.php";
require_once __DIR__ . "/src/wikitext_to_html.php";
require_once __DIR__ . "/src/WikiText/lead_section.php";
require_once __DIR__ . "/src/WikiText/index.php";
