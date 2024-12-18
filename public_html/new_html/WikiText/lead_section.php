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
    // split the wikitext into sections by == get only the first sectio
    $lead = preg_split('/==+/', $lead, 2, PREG_SPLIT_NO_EMPTY)[0];
    // ---
    $lead = refs_expend_work($lead, $wikitext);
    // ---
    return $lead;
}
