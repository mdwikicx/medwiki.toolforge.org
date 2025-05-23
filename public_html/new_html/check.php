<?php

$revid = $_GET['revid'] ?? '';

if (empty($revid)) {
    echo false;
    exit;
}

$dir_path = __DIR__ . "/revisions_new/$revid";

if (!is_dir($dir_path)) {
    echo false;
    exit;
}

$seg_exists = file_exists("$dir_path/seg.html");
$html_exists = file_exists("$dir_path/html.html");

echo $seg_exists && $html_exists;
