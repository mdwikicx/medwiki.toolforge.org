* [Live Page](https://medwiki.toolforge.org/mdtexts/)

# Overview

## segments.php

The `segments.php` script is responsible for retrieving and serving HTML content from the `segments` directory based on the `title` parameter provided in the URL. It reads the content of the specified HTML file and returns it as a JSON response. If the file does not exist, it returns an empty string.

### How it works:
1. Determines the path to the `segments` directory.
2. Retrieves the `title` parameter from the URL.
3. Reads the content of the corresponding HTML file.
4. If the file is not found, it sets the content to an empty string.
5. Returns the content as a JSON response with the key `html`.
6. Sets the `Content-Type` header to `application/json`.

