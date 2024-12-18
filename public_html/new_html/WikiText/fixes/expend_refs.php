<?php

namespace Fixes\ExpendRefs;

/*
Usage:

use function Fixes\ExpendRefs\refs_expend_work;

*/

use function WikiParse\Citations\get_full_refs;
use function WikiParse\Citations\getShortCitations;

function refs_expend_work($first, $alltext)
{
    if (empty($alltext)) {
        $alltext = $first;
    }
    $allpage_fullrefs = get_full_refs($alltext);

    $lead_fullrefs = get_full_refs($first);
    $lead_short_refs = getShortCitations($first);

    foreach ($lead_short_refs as $cite) {
        $name = $cite["name"];
        $refe = $cite["tag"];
        // ---
        if (isset($lead_fullrefs[$name])) {
            continue;
        }
        // ---
        $rr = $allpage_fullrefs[$name] ?? false;
        // ---
        if ($rr) {
            $first = str_replace($refe, $rr, $first);
        }
        // ---
    }
    return $first;
}
