<?php

namespace Lead;
/*
use function Lead\get_lead_section;
*/

use function Fixes\ExpendRefs\refs_expend_work;

function get_lead_section($wikitext)
{
    $lead = $wikitext;
    // ---
    if ($lead == '' || strpos($lead, '==') == false) {
        return $wikitext;
    }
    // ---
    // split the wikitext into sections by (lines start with ==+) get only the first section
    $leade = preg_split('/==+/', $lead, 2, PREG_SPLIT_NO_EMPTY);
    // ---
    $lead = $leade[0] ?? '';
    // ---
    if ($lead == '') {
        return $wikitext;
    }
    // ---
    $lead .= "\n==References==\n<references />";
    // ---
    $lead = refs_expend_work($lead, $wikitext);
    // ---
    return $lead;
}
