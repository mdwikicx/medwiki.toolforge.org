<?php

include_once __DIR__ . '/WikiParse/require.php';

foreach (glob(__DIR__ . "/fixes/*.php") as $filename) {
    include_once $filename;
}
/*
include_once __DIR__ . '/fixes/fix_images.php';
include_once __DIR__ . '/fixes/fix_cats.php';
include_once __DIR__ . '/fixes/del_temps.php';
include_once __DIR__ . '/fixes/fix_temps.php';
include_once __DIR__ . '/fixes/fix_langs_links.php';

include_once __DIR__ . '/fixes/del_mt_refs.php';
include_once __DIR__ . '/fixes/expend_refs.php';
include_once __DIR__ . '/fixes/ref_work.php';
*/
require_once __DIR__ . "/fix_wikitext.php";
