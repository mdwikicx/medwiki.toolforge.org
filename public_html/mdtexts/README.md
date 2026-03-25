# Overview

This script is used to generate segmentedContent to be used by [ContentTranslation tool](https://github.com/mdwikicx/cx-1).

# How it's working

This script loads the content of `Segments` from the `segments` directory already created by [Script](https://github.com/Mdwiki-TD/mdwiki-python-files/tree/update/copy_text).

## [index.html](https://medwiki.toolforge.org/mdtexts/)

The `index.html` displays the stored text status of all headings and displays `Wikitext` and `Html` and `Segments` for each page.

## [segments.php](https://medwiki.toolforge.org/mdtexts/segments.php)

The `segments.php` script is responsible for retrieving and serving HTML content from the `segments` directory based on the `title` parameter provided in the URL. It reads the content of the specified HTML file and returns it as a JSON response. If the file does not exist, it returns an empty string.

`segments` directory created by this [Script](https://github.com/Mdwiki-TD/mdwiki-python-files/tree/update/copy_text).

# Then

-   `segments.php` will be used by [ContentTranslation tool](https://github.com/mdwikicx/cx-1).
