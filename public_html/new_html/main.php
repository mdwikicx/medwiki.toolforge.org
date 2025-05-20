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
use function HtmlFixes\remove_data_parsoid;
use function NewHtml\FileHelps\get_file_dir;
use function NewHtml\FileHelps\file_write;
use function NewHtml\JsonData\get_from_json;

$printetxt = $_GET['printetxt'] ?? $_GET['print'] ?? '';

$content_types = [
    "wikitext" => "text/plain",
    "html" => "text/html",
    "seg" => "text/html",
];

$content_type = $content_types[$printetxt] ?? "application/json";

header("Content-type: $content_type");

function get_title()
{
    $title = $_GET['title'] ?? '';
    // ---
    // first litter in $title must be capital
    $title = ucfirst($title);
    // ---
    return $title;
}

function error_1($title, $revision)
{
    // send request error code using http_response_code
    http_response_code(404);
    // ---
    $data = [
        "sourceLanguage" => "en",
        "title" => $title,
        "revision" => $revision,
        "segmentedContent" => "",
        "categories" => [],
        "error_type" => "title:($title) or revision:($revision) not found",
        "error" => "No content found!",
    ];
    // ---
    return json_encode($data);
}

function get_wikitext_revision($title, $all)
{
    global $printetxt;
    // ---
    // test_print("title: $title, all: $all, printetxt: $printetxt");
    // ---
    [$wikitext, $revision] = get_wikitext($title, $all);
    // ---
    if ($wikitext == '' || $revision == '') {
        [$wikitext, $revision] = get_from_json($title, $all);
    }
    // ---
    if ($printetxt == "wikitext") {
        // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=wikitext
        echo $wikitext;
        exit();
    }
    // ---
    return [$wikitext, $revision];
}

function get_HTML_text($wikitext, $file_html, $title)
{
    global $printetxt;
    // ---
    try {
        // ---
        $HTML_text = wiki_text_to_html($wikitext, $file_html, $title);
        $HTML_text = remove_data_parsoid($HTML_text);
    } catch (Exception $e) {
        test_print("HTML generation failed for title: $title. Error: " . $e->getMessage());
        http_response_code(500);
        exit(json_encode(['error' => 'Failed to generate HTML content']));
    }
    // ---
    if ($HTML_text == $wikitext) {
        $HTML_text = '';
    }
    // ---
    if ($printetxt == "html") {
        // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=html
        echo $HTML_text;
        exit();
    }
    // ---
    return $HTML_text;
}

function get_SEG_text($HTML_text, $file_seg)
{
    global $printetxt;
    // ---
    $SEG_text = "";
    // ---
    if (!empty($HTML_text)) {
        $SEG_text = html_to_seg($HTML_text, $file_seg);
        $SEG_text = remove_data_parsoid($SEG_text);
    }
    // ---
    if ($printetxt == "seg") {
        // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=seg
        echo $SEG_text;
        exit();
    }
    // ---
    return $SEG_text;
}

function start($request, $title, $printetxt)
{
    // ---
    $all = $request['all'] ?? '';
    // if $title startwith Video then $all = 1
    if (strpos($title, 'Video') === 0) {
        $all = "1";
    }
    // ---
    [$wikitext, $revision] = get_wikitext_revision($title, $all, $printetxt);
    // ---
    // $revision = (isset($request['revision'])) ? $request['revision'] : $revision;
    // ---
    if ($wikitext == '' || $revision == '') {
        exit(error_1($title, $revision));
    }
    // ---
    $file_dir = get_file_dir($revision, $all);
    // ---
    $file_wikitext = $file_dir . "/wikitext.txt";
    $file_html     = $file_dir . "/html.html";
    $file_seg      = $file_dir . "/seg.html";
    $file_title    = $file_dir . "/title.txt";
    // ---
    file_write($file_wikitext, $wikitext);
    // ---
    file_write($file_title, $title);
    // ---
    $HTML_text = get_HTML_text($wikitext, $file_html, $title);
    // ---
    $SEG_text = "";
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
    if (empty($HTML_text)) {
        $jsonData['error_type'] = "HTML_text:() is empty";
        $jsonData['error'] = "No content found";
    } else {
        $SEG_text = get_SEG_text($HTML_text, $file_seg);
        // ---
        $jsonData['segmentedContent'] = $SEG_text;
        // ---
        if ($SEG_text == "") {
            // send request error code using http_response_code
            http_response_code(404);
            $jsonData['error_type'] = "SEG_text:($SEG_text) is empty";
            $jsonData['error'] = "No content found";
        }
    }
    // ---
    // Encode data as JSON with appropriate options
    $jsonOutput = json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    // ---
    // Output the JSON
    echo $jsonOutput;
}

$title = get_title();

if ($title == '') {
    header("Content-type: application/json");
    echo json_encode([
        'error' => 'title is empty',
    ]);
    exit(1);
}

start($_GET, $title, $printetxt);
