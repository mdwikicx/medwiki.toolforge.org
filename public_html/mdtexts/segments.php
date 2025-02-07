<?php
// تحديد مسار المجلد
$directory = __DIR__ . '/segments'; // قم بتعديل هذا المسار

$title = $_GET['title'];

$text = file_get_contents($directory . '/' . $title . '.html');

if ($text === false) {
    $text = '';
};

$data = [
    'html' => $text
];

header('Content-Type: application/json');

echo json_encode($data);
