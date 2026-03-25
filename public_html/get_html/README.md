# Overview

This script is used to generate `segmentedContent` to be used by [ContentTranslation tool](https://github.com/mdwikicx/cx-1).

# How it's working

This script processes `mdwiki.org` article and generates different formats of content based on the provided parameters. By default it will output `segmentedContent` in JSON format.

-   `printetxt` parameter can be set to any value to output the html content.

# Steps:

1. **Fetch HTML content** of the given title from [mdwiki.org REST API](https://mdwiki.org/w/rest.php/v1/page/title/html)

2. Fetch the revision id `revid` from the HTML content.

3. **Segmented Content Generation**: Generate segmented content, using [HtmltoSegments tool](https://ncc2c.toolforge.org/HtmltoSegments)

4. **JSON Data Preparation**: Prepares a JSON object with the source language, title, revision ID, segmented content, and categories.

## Output:

-   **JSON Output**: Encodes the JSON data with appropriate options and outputs it. (default)
