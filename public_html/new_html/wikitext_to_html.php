<?php

namespace Html;
/*
use function Html\wiki_text_to_html;
*/

use function Post\post_url_params_result;
use function HtmlFixes\fix_link_red;
use function HtmlFixes\del_div_error;

function change_it($text)
{
    $url = 'https://en.wikipedia.org/w/rest.php/v1/transform/wikitext/to/html/Sandbox';

    $data = ['wikitext' => $text];
    $response = post_url_params_result($url, $data);

    // Handle the response from your API
    if ($response === false) {
        error_log("API request failed: " . json_encode($data));
        return ['error' => 'Error: Could not reach API.'];
    }
    // Check if response contains an error
    if (strpos($response, ">Wikimedia Error<") !== false) {
        error_log("API returned error: $response");
        return ['error' => 'Error: Wikipedia API returned an error.'];
    }

    return ['result' => $response];
}

function wiki_text_to_html($wikitext)
{
    // ---
    $fixed = change_it($wikitext);
    // ---
    $error  = $fixed['error'] ?? '';
    $result = $fixed['result'] ?? '';
    // ---
    if ($result == '') {
        return $wikitext;
    }
    // ---
    $result = del_div_error($result);
    $result = fix_link_red($result);
    // ---
    return $result;
}
