<?php

namespace Wikitext;
/*
use function Wikitext\get_wikitext;

*/

use function Post\handle_url_request;
// use function Post\post_url_params_result;
use function FixText\fix_wikitext;
use function Lead\get_lead_section;

function get_wikitext_from_mdwiki_api($title)
{
    $params = [
        "action" => "query",
        "format" => "json",
        "prop" => "revisions",
        "titles" => $title,
        "utf8" => 1,
        "formatversion" => "2",
        "rvprop" => "content|ids"
    ];
    $url = "https://mdwiki.org/w/api.php";

    // $req = post_url_params_result($url, $params);
    $req = handle_url_request($url, 'GET', $params);
    // ---
    $json1 = json_decode($req, true);
    // ---
    $revisions = $json1["query"]["pages"][0]["revisions"][0] ?? [];

    $source = $revisions["content"] ?? '';
    $revid = $revisions["revid"] ?? '';
    // ---
    return [$source, $revid];
}

function get_wikitext_from_mdwiki_restapi($title)
{
    $title2 = str_replace("/", "%2F", $title);
    $title2 = str_replace(" ", "_", $title2);
    $url = "https://mdwiki.org/w/rest.php/v1/page/" . $title2;

    // $req = post_url_params_result($url);
    $req = handle_url_request($url, 'GET');
    $json1 = json_decode($req, true);

    $source = $json1["source"] ?? '';
    $revid = $json1["latest"]["id"] ?? '';
    // ---
    return [$source, $revid];
}

function get_wikitext($title, $all)
{
    // ---
    $json1 = get_wikitext_from_mdwiki_restapi($title);
    // ---
    $source = $json1[0];
    $revid = $json1[1];
    // ---
    // if $source match #REDIRECT [[.*?]] then get the wikitext from target page
    if (preg_match('/#REDIRECT \[\[(.*?)\]\]/i', $source, $matches)) {
        $title = $matches[1];
        test_print("Redirecting to: $title\n");
        $json1 = get_wikitext_from_mdwiki_restapi($title);
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
        test_print("wikitext empty!.");
    };
    // ---
    return [$source, $revid];
}
