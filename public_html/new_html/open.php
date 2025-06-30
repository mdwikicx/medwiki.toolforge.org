<?php

require_once __DIR__ . "/require.php";

use function HtmlFixes\remove_data_parsoid;

$revid = $_GET['revid'] ?? '';

$file = $_GET['file'] ?? '';

$content_type = ($file == 'wikitext.txt') ? "text/plain" : "text/html";

header("Content-type: $content_type");

$file_path = __DIR__ . "/revisions_new/$revid/$file";

$text = file_get_contents($file_path) ?? '';

if (!empty($text)) {
    if ($file == "seg.html" || $file == "html.html") {
        $text = remove_data_parsoid($text);
    }
}

echo $text;
