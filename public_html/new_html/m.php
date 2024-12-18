<?PHP

namespace HtmlFixes;
use DOMDocument;
/*

use function HtmlFixes\fix_links;

*/

function fix_links($html)
{

    // find and remove from this: (if href has (action=edit))

    // <a rel="mw:WikiLink" href="./Cancer_signs_and_symptoms?action=edit&amp;redlink=1" title="Cancer signs and symptoms" class="new" typeof="mw:LocalizedAttrs" data-mw-i18n='{"title":{"lang":"x-page","key":"red-link-title","params":["Cancer signs and symptoms"]}}' id="mwBA">symptoms</a>

    // to this:

    // <a rel="mw:WikiLink" href="./Cancer_signs_and_symptoms" title="Cancer signs and symptoms" id="mwBg">symptoms</a>

    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    // Find all link elements with href attributes containing "action=edit"
    $links = $dom->getElementsByTagName('a');

    // Loop through each anchor element
    foreach ($links as $link) {
        // Check if the href attribute contains 'action=edit'
        $href = $link->getAttribute('href');
        if (strpos($href, 'action=edit') !== false) {
            $newHref = preg_replace('/\?action=edit.*?/', '', $href);
            $link->setAttribute('href', $newHref);

            // Remove unwanted attributes: typeof, data-mw-i18n
            $link->removeAttribute('typeof');
            $link->removeAttribute('data-mw-i18n');
            $link->removeAttribute('class');

            // remove class new
            $link->setAttribute('class', 'cx-link');
        }
    }

    // Return the modified HTML content
    return $dom->saveHTML();
}


function remove_all_style_tags($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $elements = $dom->getElementsByTagName('style');
    foreach ($elements as $element) {
        $element->parentNode->removeChild($element);
    }
    $content = $dom->saveHTML($dom->documentElement);
    return $content;
}

function dom_it($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $content = $dom->saveHTML($dom->documentElement);

    return $content;
}
