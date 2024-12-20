<?php

namespace Post;
/*

use function Post\get_url_params_result;
use function Post\post_url_params_result;

*/
// https://mdwiki.org/w/rest.php/v1/page/Sympathetic_crashing_acute_pulmonary_edema/html
// https://mdwiki.org/w/rest.php/v1/revision/1420795/html

$usr_agent = 'WikiProjectMed Translation Dashboard/1.0 (https://medwiki.toolforge.org/; tools.medwiki@toolforge.org)';

function get_url_params_result(string $url): string
{
    global $usr_agent;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $output = curl_exec($ch);
    if ($output === FALSE) {
        error_log("<br>\ncURL Error: " . curl_error($ch) . "<br>\n$url\n<br>");
    }

    curl_close($ch);
    return $output;
}

function post_url_params_result(string $endPoint, array $params = []): string
{
    global $usr_agent;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $output = curl_exec($ch);
    // $url = "{$endPoint}?" . http_build_query($params);
    // if ($output === FALSE) { echo ("<br>cURL Error: " . curl_error($ch) . "<br>$url"); }

    // Check HTTP response code
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code !== 200) {
        error_log("API returned HTTP $http_code: $http_code");
        // return ['error' => "Error: API returned HTTP $http_code"];
    }
    curl_close($ch);
    return $output;
}
