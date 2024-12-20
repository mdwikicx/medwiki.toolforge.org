<?php

namespace Wikitext;
/*
use function Wikitext\get_wikitext;

*/

use function Post\get_url_params_result;
use function FixText\fix_wikitext;
use function Lead\get_lead_section;

function get_wikitext_from_mdwiki($title)
{
    $title2 = str_replace("/", "%2F", $title);
    $title2 = str_replace(" ", "_", $title2);
    $url = "https://mdwiki.org/w/rest.php/v1/page/" . $title2;

    $req = get_url_params_result($url);
    $json1 = json_decode($req, true);

    $source = $json1["source"] ?? '';
    $revid = $json1["latest"]["id"] ?? '';
    // ---
    return [$source, $revid];
}

function get_wikitext($title, $all)
{
    // ---
    $json1 = get_wikitext_from_mdwiki($title);
    // ---
    $source = $json1[0];
    $revid = $json1[1];
    // ---
    // if $source match #REDIRECT [[.*?]] then get the wikitext from target page
    if (preg_match('/#REDIRECT \[\[(.*?)\]\]/i', $source, $matches)) {
        $title = $matches[1];
        error_log("Redirecting to: $title\n");
        $json1 = get_wikitext_from_mdwiki($title);
        $source = $json1[0];
        $revid = $json1[1];
    }
    // ---
    if ($source != '') {
        // ---
        if ($all == '') {
            $source = get_lead_section($source);
        }
        // ---
        $source = fix_wikitext($source, $title);
    }
    // ---
    if ($source == "") {
        error_log("wikitext empty!.");
    };
    // ---
    return [$source, $revid];
}
