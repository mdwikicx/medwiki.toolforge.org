<?php
header("Access-Control-Allow-Origin: *");
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . "/require.php";

use function Wikitext\get_wikitext;
use function Segments\html_to_seg;
use function Html\wiki_text_to_html;

$title = $_GET['title'] ?? '';
$all = $_GET['all'] ?? '';
$printetxt = $_GET['printetxt'] ?? '';

if ($title == '') {
    header("Content-type: application/json");
    echo json_encode([
        'error' => 'title is empty',
    ]);
    exit(1);
}

$get_it = get_wikitext($title, $all);

$wikitext = ($get_it[0] != '') ? $get_it[0] : 'empty text!';
$revision = $get_it[1];

if ($printetxt == "wikitext") {
    header("Content-type: text/plain");
    // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=wikitext
    echo $wikitext;
    exit();
}

$HTML_text = wiki_text_to_html($wikitext);

if ($printetxt == "html") {
    header("Content-type: text/html");
    // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=html
    echo $HTML_text;
    exit();
}

if ($HTML_text != '' && $HTML_text != $wikitext) {
    $HTML_text = html_to_seg($HTML_text);
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
    header("Content-type: text/html");
    // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=seg
    echo $HTML_text;
    exit();
}
// ---
// Encode data as JSON with appropriate options
$jsonOutput = json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
// $jsonOutput = json_encode($jsonData);

header("Content-type: application/json");

// Output the JSON
echo $jsonOutput;
