<?php

namespace Html;
/*
use function Html\wiki_text_to_html;
*/

use function Post\post_url_params_result;
use function HtmlFixes\fix_link_red;
use function HtmlFixes\del_div_error;

function change_it($text, $title)
{
    $url = "https://en.wikipedia.org/w/rest.php/v1/transform/wikitext/to/html/Sandbox";
    $url = "https://en.wikipedia.org/w/rest.php/v1/transform/wikitext/to/html/$title";

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

function do_wiki_text_to_html($wikitext, $title)
{
    // ---
    if ($wikitext == '') {
        return "";
    }
    // ---
    $fixed = change_it($wikitext, $title);
    // ---
    $error  = $fixed['error'] ?? '';
    $result = $fixed['result'] ?? '';
    // ---
    if ($result == '') {
        return "";
    }
    // ---
    $result = del_div_error($result);
    $result = fix_link_red($result);
    // ---
    return $result;
}

function wiki_text_to_html($wikitext, $file_html, $title)
{
    // ---
    if (file_exists($file_html)) {
        $HTML_text = file_get_contents($file_html);
        if ($HTML_text != '') {
            return $HTML_text;
        }
    }
    // ---
    if ($wikitext == '') {
        return "";
    }
    // ---
    $result = do_wiki_text_to_html($wikitext, $title);
    // ---
    if ($result == '') {
        return "";
    }
    // ---
    try {
        file_put_contents($file_html, $result);
    } catch (\Exception $e) {
        error_log("Error: Could not write to file: $file_html");
    }
    // ---
    return $result;
}
