<?php

namespace Wikitext;
/*
use function Wikitext\get_wikitext;

*/

if (isset($_GET['test'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

use function Post\get_url_params_result;

function get_wikitext($title)
{
    $title2 = str_replace("/", "%2F", $title);
    $url = "https://mdwiki.org/w/rest.php/v1/page/" . $title2;

    $req = get_url_params_result($url);
    $json1 = json_decode($req, true);

    $source = $json1["source"] ?? '';
    $revid = $json1["latest"]["id"] ?? '';
    // ---
    return [$source, $revid];
}
