<?php
header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . "/require.php";

use function Wikitext\get_wikitext;
use function FixText\fix_wikitext;
use function Lead\get_lead_section;
use function Segments\html_to_seg;
use function Html\wiki_text_to_html;

$title = $_GET['title'] ?? '';
$all = $_GET['all'] ?? '';

if ($title == '') {
    echo json_encode([
        'error' => 'title is empty',
    ]);
    exit(1);
}

$get_it = get_wikitext($title);

$wikitext = $get_it[0];
$revision = $get_it[1];

$wikitext = fix_wikitext($wikitext, $title);

$lead_section = $wikitext;

if ($all == '') {
    $lead_section = get_lead_section($wikitext);
}

// echo json_encode([
//     'wikitext' => $wikitext,
//     'lead_section' => $lead_section,
//     'revision' => $revision,
// ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// exit();

$HTML_text = wiki_text_to_html($lead_section);

if ($HTML_text != '' && $HTML_text != $lead_section) {
    $HTML_text = html_to_seg($HTML_text);
    // $HTML_text = remove_all_style_tags($HTML_text);
    // $HTML_text = dom_it($HTML_text);
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
// Encode data as JSON with appropriate options
$jsonOutput = json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
// $jsonOutput = json_encode($jsonData);

// Output the JSON
echo $jsonOutput;
