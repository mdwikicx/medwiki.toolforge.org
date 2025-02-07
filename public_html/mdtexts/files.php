<?php

$directory = __DIR__  . '/segments';

$files = scandir($directory);

$files = array_diff($files, array('.', '..'));

header('Content-Type: application/json');

echo json_encode(array_values($files));
