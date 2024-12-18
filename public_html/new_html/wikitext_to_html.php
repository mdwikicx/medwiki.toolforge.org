<?php

namespace Html;
/*
use function Html\wiki_text_to_html;
*/

use function Post\post_url_params_result;
use function HtmlFixes\fix_links;

function change_it($text)
{
    $url = 'https://en.wikipedia.org/w/rest.php/v1/transform/wikitext/to/html/Sandbox';

    $data = ['wikitext' => $text];
    $response = post_url_params_result($url, $data);

    // Handle the response from your API
    if ($response === false) {
        return ['error' => 'Error: Could not reach API.'];
    }

    // Check if response contains an error
    if (strpos($response, ">Wikimedia Error<") !== false) {
        return ['error' => 'Error: at API.'];
    }
    return ['result' => $response];
}

function wiki_text_to_html($wikitext)
{
    // ---
    $fixed = change_it($wikitext);
    // ---
    $error  = $error['error'] ?? '';
    $result = $fixed['result'] ?? '';
    // ---
    if ($result == '') {
        return $wikitext;
    }
    // ---
    // $result = fix_links($result);
    // ---
    return $result;
}
