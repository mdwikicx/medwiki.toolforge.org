<?php

include_once __DIR__ . '/WikiParse/Template.php';
include_once __DIR__ . '/WikiParse/Citations.php';
include_once __DIR__ . '/WikiParse/Category.php';

include_once __DIR__ . '/fixes/fix_images.php';
include_once __DIR__ . '/fixes/fix_cats.php';
include_once __DIR__ . '/fixes/fix_temps.php';
include_once __DIR__ . '/fixes/fix_langs_links.php';

include_once __DIR__ . '/fixes/del_mt_refs.php';
include_once __DIR__ . '/fixes/expend_refs.php';
include_once __DIR__ . '/fixes/ref_work.php';

require_once __DIR__ . "/fix_wikitext.php";
