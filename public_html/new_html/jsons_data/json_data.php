<?php

namespace NewHtml\JsonData;

/*
use:
use function NewHtml\JsonData\get_title_revision;
use function NewHtml\JsonData\add_title_revision;
use function NewHtml\JsonData\get_from_json;
use function NewHtml\JsonData\get_Data;
*/

use function NewHtml\FileHelps\get_file_dir;
use function NewHtml\FileHelps\file_write;
use function NewHtml\FileHelps\read_file;

$json_file = __DIR__ . "/json_data.json";
$json_file_all = __DIR__ . "/json_data_all.json";

if (!file_exists($json_file)) {
    file_write($json_file, '{}');
}

if (!file_exists($json_file_all)) {
    file_write($json_file_all, '{}');
}

function get_Data($tyt)
{
    global $json_file_all, $json_file;
    // ---
    $json_file = ($tyt == 'all') ? $json_file_all : $json_file;
    // ---
    $file_text = read_file($json_file);
    // ---
    if ($file_text == '') return [];
    // ---
    $data = json_decode($file_text, true) ?? [];
    // ---
    return $data;
}

function get_title_revision($title, $all)
{
    global $json_file_all, $json_file;
    // ---
    $file = (!empty($all)) ? $json_file_all : $json_file;
    // ---
    $file_text = read_file($file);
    // ---
    if ($file_text == '') return '';
    // ---
    $title_revision = json_decode($file_text, true);
    // ---
    if (!is_array($title_revision)) return '';
    // ---
    if (array_key_exists($title, $title_revision)) {
        return $title_revision[$title];
    }
    // ---
    return "";
}

function add_title_revision($title, $revision, $all)
{
    global $json_file_all, $json_file;
    // ---
    if (empty($title) || empty($revision)) return '';
    // ---
    $file = (!empty($all)) ? $json_file_all : $json_file;
    // ---
    $file_text = read_file($file);
    // ---
    if ($file_text == '') return '';
    // ---
    $data = json_decode($file_text, true);
    // ---
    if (!is_array($data)) return '';
    // ---
    $data[$title] = $revision;
    // ---
    file_write($file, json_encode($data));
    // ---
    return $data;
}

function get_from_json($title, $all)
{
    $revid = get_title_revision($title, $all);
    // ---
    if (empty($revid)) return ['', ''];
    // ---
    $file_dir = get_file_dir($revid, $all);
    // ---
    $wikitext = read_file($file_dir . "/wikitext.txt");
    // ---
    return [$wikitext, $revid];
}
