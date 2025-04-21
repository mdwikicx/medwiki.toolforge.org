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

// ---
// first litter in $title must be capital
$title = ucfirst($title);
// ---
// if $title startwith Video then $all = 1
if (strpos($title, 'Video') === 0) {
    $all = "1";
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
$HTML_text = "";
$SEG_text = "";
// ---
// $file_dir_old = __DIR__ . "/revisions";
// $file_html_old = ($all != '') ? $file_dir_old . "/html/$revision" . "_all.html" : $file_dir_old . "/html/$revision.html";
// $file_seg_old  = ($all != '') ? $file_dir_old . "/seg/$revision" . "_all.html" : $file_dir_old . "/seg/$revision.html";
// ---
if ($wikitext == '' || $revision == '') {
    // send request error code using http_response_code
    http_response_code(404);
    $jsonData = [
        "sourceLanguage" => "en",
        "title" => $title,
        "revision" => $revision,
        "segmentedContent" => $SEG_text,
        "categories" => []
    ];
    $jsonData['error'] = "No content found!";
    exit(json_encode($jsonData));
}
// ---
$file_dir = __DIR__ . "/revisions_new/$revision";
// ---
if ($all != '') $file_dir .= "_all";
// ---
if (!is_dir($file_dir)) {
    if (!mkdir($file_dir, 0777, true)) {
        test_print(sprintf('Failed to create directory "%s".', $file_dir));
    }
}
// ---
$file_wikitext = $file_dir . "/wikitext.txt";
$file_html     = $file_dir . "/html.html";
$file_seg      = $file_dir . "/seg.html";
$file_title    = $file_dir . "/title.txt";
// ---
try {
    try {
        file_put_contents($file_wikitext, $wikitext);
    } catch (\Exception $e) {
        test_print("Error: Could not write to file: $file_wikitext");
    }
    try {
        file_put_contents($file_title, $title);
    } catch (\Exception $e) {
        test_print("Error: Could not write to file: $file_title");
    }
    // ---
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
    $SEG_text = html_to_seg($HTML_text, $file_seg);
}
// ---
// print_data($revision, $SEG_text, $sourcelanguage, $title, $error = $error);

$jsonData = [
    "sourceLanguage" => "en",
    "title" => $title,
    "revision" => $revision,
    "segmentedContent" => $SEG_text,
    "categories" => []
];
// ---
if ($printetxt == "seg") {
    // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=seg
    echo $SEG_text;
    exit();
}
// ---
if ($SEG_text == "") {
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
