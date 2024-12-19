<?PHP

namespace HtmlFixes;

use DOMDocument;
/*

use function HtmlFixes\fix_links;
use function HtmlFixes\del_div_error;
use function HtmlFixes\fix_link_red;

*/
// <div([^\/>]*?)>(.+?)<\/div>
// class="error"

function del_div_error($html)
{

    preg_match_all("/<div([^\/>]*?)>(.+?)<\/div>/is", $html, $matches);
    // ---
    foreach ($matches[1] as $key => $options) {
        // $content = $matches[2][$key];
        $cite_text = $matches[0][$key];
        if (preg_match("/class=[\"']error[\"']/is", $options)) {
            $html = str_replace($cite_text, '', $html);
        }
    }
    // ---
    return $html;
}

function get_attrs($text)
{
    $text = "<ref $text>";
    $attrfind_tolerant = '/((?<=[\'"\s\/])[^\s\/>][^\s\/=>]*)(\s*=+\s*(\'[^\']*\'|"[^"]*"|(?![\'"])[^>\s]*))?(?:\s|\/(?!>))*/';
    $attrs = [];

    if (preg_match_all($attrfind_tolerant, $text, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $attr_name = strtolower($match[1]);
            $attr_value = isset($match[3]) ? $match[3] : "";
            $attrs[$attr_name] = $attr_value;
        }
    }
    // ---
    // var_export($attrs);
    // ---
    return $attrs;
}

function fix_link_red($html)
{

    preg_match_all("/<a([^>]*?)>(.+?)<\/a>/is", $html, $matches);
    // ---
    foreach ($matches[1] as $key => $options) {
        $content = $matches[2][$key];
        $cite_text = $matches[0][$key];
        if (preg_match("/mw:LocalizedAttrs/is", $options)) {
            // ---
            $attrs = get_attrs($options);
            // ---
            $href = $attrs['href'] ?? '';
            // ---
            if (strpos($href, 'action=edit') !== false) {
                $newHref = preg_replace('/\?action=edit.*?/', '', $href);
                $newHref = str_replace('&amp;redlink=1', '', $newHref);
                $newHref = str_replace('&redlink=1', '', $newHref);
                // ---
                $attrs['href'] = $newHref;
                // ---
                $attrs_to_del = ['typeof', 'data-mw-i18n', 'class'];

                foreach ($attrs_to_del as $attr) {
                    if (isset($attrs[$attr])) {
                        unset($attrs[$attr]);
                    }
                }
                // ---
            }
            // ---
            $new_attrs = implode(' ', array_map(
                function ($key, $value) {
                    return "$key=$value";
                },
                array_keys($attrs),
                array_values($attrs)
            ));
            // ---
            $new_cite_text = "<a $new_attrs>$content</a>";
            // ---
            $html = str_replace($cite_text, $new_cite_text, $html);
        }
    }
    // ---
    return $html;
}

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
