<?php

namespace Segments;
/*
use function Segments\html_to_seg;
*/

use function Post\handle_url_request;
// use function Post\post_url_params_result;

function change_html_to_seg($text)
{
    $url = 'https://ncc2c.toolforge.org/HtmltoSegments';

    $data = ['html' => $text];
    // $response = post_url_params_result($url, $data);
    $response = handle_url_request($url, 'POST', $data);

    // Handle the response from your API
    if ($response === false) {
        test_print("API request failed: " . json_encode($data));
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

function do_html_to_seg($text)
{
    // ---
    $fixed = change_html_to_seg($text);
    // ---
    // $error  = $fixed['error'] ?? '';
    $result = $fixed['result'] ?? "";
    // ---
    // $result = str_replace("https://medwiki.toolforge.org/md/", "https://en.wikipedia.org/w/", $result);
    // $result = str_replace("https://medwiki.toolforge.org/w/", "https://en.wikipedia.org/w/", $result);
    // $result = str_replace("https://medwiki.toolforge.org/wiki/", "https://en.wikipedia.org/wiki/", $result);
    // ---
    if ($result == '') {
        return "";
    }
    // ---
    return $result;
}

function html_to_seg($text, $file_seg)
{
    // ---
    if (file_exists($file_seg)) {
        $text = file_get_contents($file_seg);
        if ($text != '') {
            return $text;
        }
    }
    // ---
    $result = do_html_to_seg($text);
    // ---
    if ($result == '') {
        return "";
    }
    // ---
    try {
        file_put_contents($file_seg, $result);
    } catch (\Exception $e) {
        test_print("Error: Could not write to file: $file_seg");
    }
    // ---
    return $result;
}
