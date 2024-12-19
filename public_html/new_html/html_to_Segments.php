<?php

namespace Segments;
/*
use function Segments\html_to_seg;
*/

use function Post\post_url_params_result;

function change_html_to_seg($text)
{
    $url = 'https://ncc2c.toolforge.org/textp';

    $data = ['html' => $text];
    $response = post_url_params_result($url, $data);

    // Handle the response from your API
    if ($response === false) {
        error_log("API request failed: " . json_encode($data));
        return ['error' => 'Error: Could not reach API.'];
    }

    $data = json_decode($response, true);
    if (isset($data['error'])) {
        return ['error' => 'Error: ' . $data['error']];
    }

    // Extract the result from the API response
    if (isset($data['result'])) {
        return ['result' => $data['result']];
    } else {
        return ['error' => 'Error: Unexpected response format.'];
    }
}

function html_to_seg($text)
{
    // ---
    $fixed = change_html_to_seg($text);
    // ---
    $error  = $fixed['error'] ?? '';
    $result = $fixed['result'] ?? $text;
    // ---
    // $result = str_replace("https://medwiki.toolforge.org/md/", "https://en.wikipedia.org/w/", $result);
    // $result = str_replace("https://medwiki.toolforge.org/w/", "https://en.wikipedia.org/w/", $result);
    // $result = str_replace("https://medwiki.toolforge.org/wiki/", "https://en.wikipedia.org/wiki/", $result);
    // ---
    return $result;
}
