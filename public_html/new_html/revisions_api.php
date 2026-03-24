<?php

/**
 * Reisions API
 *
 * Returns JSON data for revisions dashboard.
 *
 * @package MDWiki\NewHtml
 */

define('DEBUGX', true); // Set APP_DEBUG=1 in development

if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require_once __DIR__ . "/new_html_src/file_helps.php";
require_once __DIR__ . "/json_data.php";

use function NewHtml\FileHelps\get_revisions_new_dir;
use function NewHtml\JsonData\get_Data;
use function NewHtml\JsonData\dump_both_data;

$revisions_new_dir = get_revisions_new_dir();

$dirs = array_filter(glob($revisions_new_dir . '/*/'), 'is_dir');
// sort directories by last modified date
usort($dirs, function ($a, $b) {
    $timeA = is_file($a . '/wikitext.txt') ? filemtime($a . '/wikitext.txt') : filemtime($a);
    $timeB = is_file($b . '/wikitext.txt') ? filemtime($b . '/wikitext.txt') : filemtime($b);
    return $timeB - $timeA;
});

$results = [];

$number = 0;

$main_data = get_Data('');
$main_data_all = get_Data('all');

$make_dump = empty($main_data);

foreach ($dirs as $dir) {
    $number += 1;

    $wikitextFile = $dir . '/wikitext.txt';
    $lastModified = is_file($wikitextFile)
        ? date('Y-m-d H:i', filemtime($wikitextFile))
        : date('Y-m-d H:i', filemtime($dir));

    $dir = rtrim($dir, '/');
    $dir_path = basename($dir);
    $oldid_number = str_replace('_all', '', $dir_path);

    $files = array_filter(glob("$dir/*"), 'is_file');
    $files = array_map('basename', $files);

    $wikitext_exists = in_array('wikitext.txt', $files);
    $html_exists = in_array('html.html', $files);
    $seg_exists = in_array('seg.html', $files);

    $title_path = "$dir/title.txt";
    $title = (is_file($title_path)) ? file_get_contents($title_path) : '';
    $title = str_replace('_', ' ', $title);

    if (!empty($title) && $make_dump && !empty($oldid_number)) {
        $id = (int)$oldid_number ?? 0;
        if ($id > 0) {
            if (strpos($dir_path, '_all') !== false) {
                $main_data_all[$title] = $id;
            } else {
                $main_data[$title] = $id;
            }
        }
    }

    $results[] = [
        'number' => $number,
        'lastModified' => $lastModified,
        'title' => $title,
        'dir_path' => $dir_path,
        'oldid_number' => $oldid_number,
        'wikitext_exists' => $wikitext_exists,
        'html_exists' => $html_exists,
        'seg_exists' => $seg_exists
    ];
}


if ($make_dump) {
    dump_both_data($main_data, $main_data_all);
}

header('Content-Type: application/json');
echo json_encode(['results' => $results]);
