<?php
header("Access-Control-Allow-Origin: *");
if (isset($_GET['test'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

require_once __DIR__ . "/require.php";

use function Wikitext\get_wikitext;
use function Segments\html_to_seg;
use function Html\wiki_text_to_html;

$title = $_GET['title'] ?? '';
$all = $_GET['all'] ?? '';
$printetxt = $_GET['printetxt'] ?? $_GET['print'] ?? '';

if ($title == '') {
    header("Content-type: application/json");
    echo json_encode([
        'error' => 'title is empty',
    ]);
    exit(1);
}

$get_it = get_wikitext($title, $all);

$wikitext = ($get_it[0] != '') ? $get_it[0] : '';
$revision = $get_it[1];

$content_types = [
    "wikitext" => "text/plain",
    "html" => "text/html",
    "seg" => "text/html",
];

$content_type = $content_types[$printetxt] ?? "application/json";
header("Content-type: $content_type");
// ---
if ($printetxt == "wikitext") {
    // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=wikitext
    echo $wikitext;
    exit();
}
// ---
$file_dir = __DIR__ . "/revisions";
// ---
$file_html = ($all != '') ? $file_dir . "/html/$revision" . "_all.html" : $file_dir . "/html/$revision.html";
$file_seg  = ($all != '') ? $file_dir . "/seg/$revision" . "_all.html" : $file_dir . "/seg/$revision.html";
// ---
$HTML_text = "";
// ---
try {
    $HTML_text = wiki_text_to_html($wikitext, $file_html, $title);
} catch (Exception $e) {
    test_print("HTML generation failed for title: $title. Error: " . $e->getMessage());
    http_response_code(500);
    exit(json_encode(['error' => 'Failed to generate HTML content']));
}
// ---
if ($printetxt == "html") {
    // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=html
    echo $HTML_text;
    exit();
}

if ($HTML_text != '' && $HTML_text != $wikitext) {
    $HTML_text = html_to_seg($HTML_text, $file_seg);
}

// print_data($revision, $HTML_text, $sourcelanguage, $title, $error = $error);

$jsonData = [
    "sourceLanguage" => "en",
    "title" => $title,
    "revision" => $revision,
    "segmentedContent" => $HTML_text,
    "categories" => []
];
// ---
if ($printetxt == "seg") {
    // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=seg
    echo $HTML_text;
    exit();
}
// ---
if ($HTML_text == "") {
    // send request error code using http_response_code
    http_response_code(404);
    $jsonData['error'] = "No content found";
}

// ---
// Encode data as JSON with appropriate options
$jsonOutput = json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
// $jsonOutput = json_encode($jsonData);

// Output the JSON
echo $jsonOutput;
