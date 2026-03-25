# Static Analysis Report - Medwiki Toolforge Project

**Analysis Date:** 2026-02-14
**Analyzer:** Claude Code
**PHP Version Target:** 8.0+

---

## Executive Summary

This report documents findings from a comprehensive static analysis of the Medwiki codebase. The analysis identified **3 critical vulnerabilities**, **5 logical errors**, **4 performance bottlenecks**, and **6 architectural anti-patterns**.

### Severity Distribution

| Severity | Count | Category |
|----------|-------|----------|
| Critical | 3 | Security |
| High | 5 | Logical Errors |
| Medium | 4 | Performance |
| Low | 6 | Architectural |

---

## 1. Security Vulnerabilities

### 1.1 CRITICAL: Hardcoded Database Credentials

**File:** `public_html/db404.php:9-14`

```php
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    $host = 'localhost:3306';
    $dbname = 'vi';
    $user = 'root';
    $password = 'root11';  // CRITICAL: Hardcoded credential
}
```

**Risk:** Credential exposure in version control history.
**Recommendation:** Use environment variables or a secrets manager even for development.

### 1.2 CRITICAL: Path Traversal Vulnerability

**File:** `public_html/mdtexts/segments.php:4-6`

```php
$title = $_GET['title'];
$text = file_get_contents($directory . '/' . $title . '.html');
```

**Risk:** An attacker can use `title=../../../etc/passwd%00` to read arbitrary files.
**Recommendation:** Validate and sanitize the title parameter:

```php
$title = basename($_GET['title']);
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $title)) {
    http_response_code(400);
    exit('Invalid title');
}
```

### 1.3 HIGH: Server-Side Request Forgery (SSRF) Potential

**File:** `public_html/mdwiki_api.php:5-11`

```php
$wmcloud = $_GET['wmcloud'] ?? ($_POST['wmcloud'] ?? '');
$api_url = "https://mdwiki.org/w/api.php";
if ($wmcloud !== '') {
    $api_url = "https://mdwiki.wmcloud.org/w/api.php";
}
```

**Risk:** Limited SSRF via `wmcloud` parameter - only two domains allowed, but no validation of the parameter value.
**Recommendation:** Use strict boolean validation:

```php
$wmcloud = filter_input(INPUT_GET, 'wmcloud', FILTER_VALIDATE_BOOLEAN)
    ?? filter_input(INPUT_POST, 'wmcloud', FILTER_VALIDATE_BOOLEAN);
```

### 1.4 HIGH: Reflected XSS via Dynamic Script Loading

**File:** `public_html/TranslationMdwiki/index.php:93-95`

```php
if (isset($js_list[$js])) {
    echo "<script src='$js_list[$js]'></script>";
}
```

**Risk:** While `$js_list` is hardcoded, the `js` parameter from `$_GET['js']` (line 79) could be manipulated if `$js_list` keys contain special characters.
**Recommendation:** HTML-encode all dynamic output:

```php
echo '<script src="' . htmlspecialchars($js_list[$js], ENT_QUOTES, 'UTF-8') . '"></script>';
```

### 1.5 MEDIUM: Insecure Cookie File Handling

**File:** `public_html/post.php:21-22`

```php
curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
```

**Risk:**
- Cookie file created in current working directory with default permissions
- Potential race condition in multi-process environment
- No cleanup of cookie file

**Recommendation:** Use `sys_get_temp_dir()` with unique filenames:

```php
$cookieFile = sys_get_temp_dir() . '/medwiki_cookies_' . getmypid() . '.txt';
```

### 1.6 MEDIUM: Missing Input Validation

**File:** `public_html/get_html/index.php:15-19`

```php
$sourcelanguage = $_GET['sourcelanguage'] ?? 'en';
$title = $_GET['title'] ?? '';
$revision = $_GET['revision'] ?? '';
```

**Risk:** No validation of input format/length. Malicious inputs could cause unexpected behavior.
**Recommendation:** Add input validation:

```php
$title = substr($_GET['title'] ?? '', 0, 255);
if (!preg_match('/^[a-zA-Z0-9_\/\-%]+$/', $title)) {
    http_response_code(400);
    exit(json_encode(['error' => 'Invalid title format']));
}
```

---

## 2. Logical Errors (Bugs)

### 2.1 CRITICAL: Variable Assignment Bug

**File:** `public_html/get_html/fixiit.php:77`

```php
$error  = $error['error'] ?? '';  // BUG: $error is undefined, should be $fixed
$result = $fixed['result'] ?? $text;
```

**Impact:** This bug causes `$error` to always be empty string, bypassing the error check on line 84.
**Fix:**

```php
$error  = $fixed['error'] ?? '';
```

### 2.2 HIGH: Missing Return Value Check

**File:** `public_html/get_html/post.php:70-77`

```php
try {
    $res = get_url_params_result($url);
    if ($res) {
        $text = $res;
    }
} catch (Exception $e) {
    $text = "";
};
```

**Issue:** `get_url_params_result` returns `string`, not `false` on failure. It returns `curl_exec` result which can be empty string on failure. The condition `if ($res)` will be false for empty strings but true for error pages.

### 2.3 HIGH: Inconsistent Error Response Format

**File:** `public_html/get_html/index.php:36-43`

```php
if ($test_js != false && isset($test_js['errorKey'])) {
    $HTML_text = "";
    $message = $test_js['messageTranslations']['en'] ?? 'The specified title does not exist';
    print_data($revision, $HTML_text, $sourcelanguage, $title, $error = $message);
    header("Content-type: application/json");  // BUG: header() after print_data() which outputs content
    exit(1);
}
```

**Issue:** `header()` is called after `print_data()` has already output content. Headers cannot be set after output starts.
**Fix:** Move header before `print_data()`.

### 2.4 MEDIUM: Potential Division by Zero in Section Counting

**File:** `public_html/get_html/m.php:115-117`

```php
function get_section0_old($HTML_text)
{
    if (count_sections($HTML_text) < 3) {
        return $HTML_text;
    }
```

**Issue:** While not causing an error, the logic assumes at least 3 sections for section extraction. Pages with 1-2 sections return full HTML, which may not be intended behavior.

### 2.5 LOW: Unreachable Error State

**File:** `public_html/get_html/fixiit.php:84-86`

```php
if ($error == "" && is_bad_fix($result) == false && $revision != '') {
    file_put_contents($file, $result);
}
```

**Issue:** Due to bug 2.1, `$error` is always empty string, so the first condition always passes.

---

## 3. Performance Bottlenecks

### 3.1 HIGH: Multiple DOMDocument Parsing

**File:** `public_html/get_html/m.php`

Each function (`fix_links`, `remove_unlinkedwikibase`, `get_0_section`, `get_references_section`, `count_sections`, `remove_templatestyles`, `remove_temp_Distinguish`, `dom_it`) creates and parses a new DOMDocument:

```php
function fix_links($html) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    // ...
}

function remove_unlinkedwikibase($html) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    // ...
}
```

In `do_changes()`, this means the same HTML is parsed 4-5 times:
1. `get_section0()` → parses DOM
2. `del_div_error()` → uses regex (not DOM)
3. `remove_unlinkedwikibase()` → parses DOM
4. `remove_temp_Distinguish()` → parses DOM
5. Final `dom_it()` → parses DOM

**Impact:** O(n) complexity multiplied by number of parsing operations.
**Recommendation:** Parse once and pass DOMDocument object between functions.

### 3.2 MEDIUM: No Caching Headers

**File:** `public_html/get_html/index.php`

API responses lack caching headers, forcing clients to re-fetch content:

```php
header("Content-type: application/json");
// Missing: Cache-Control, ETag, Last-Modified
```

**Recommendation:** Add caching headers based on revision ID:

```php
header("Cache-Control: public, max-age=86400");
header("ETag: \"$revision\"");
```

### 3.3 MEDIUM: Synchronous External API Calls

**File:** `public_html/get_html/fixiit.php:65-88`

The `fix_it()` function makes synchronous cURL calls to external service, blocking execution:

```php
$fixed = do_fix_it($text);  // Blocking call
```

**Recommendation:** Consider:
- Using curl_multi for parallel requests
- Implementing request queue with background processing
- Adding circuit breaker pattern

### 3.4 LOW: File System Operations Without Batching

**File:** `public_html/mdtexts/files.php`

```php
$files = scandir($directory);
$files = array_diff($files, array('.', '..'));
```

**Issue:** For directories with many files, this loads all filenames into memory.
**Recommendation:** Use `FilesystemIterator` with filtering for better memory efficiency.

---

## 4. Architectural Anti-Patterns

### 4.1 Global State Usage

**Files:** `public_html/get_html/post.php`, `public_html/post.php`

```php
global $usr_agent;
```

**Issue:** Global variables make testing difficult and create implicit dependencies.
**Recommendation:** Use dependency injection or class constants.

### 4.2 Code Duplication

**Files:**
- `public_html/post.php:11-30` and `public_html/get_html/post.php:8-22`

Nearly identical cURL functions exist in both files.

**Recommendation:** Extract to shared `Httpclient.php` utility class.

### 4.3 Missing Error Handling Pattern

Throughout the codebase, errors are handled inconsistently:
- Some functions return `false`
- Some return empty string
- Some return error arrays
- Some use `try/catch` without re-throwing

**Recommendation:** Implement consistent error handling:

```php
class ApiException extends Exception {}
class NetworkException extends Exception {}

function get_text_html(string $title, string $revision): string {
    throw new NetworkException("Connection timeout");
}
```

### 4.4 No Response Standardization

**Files:** `public_html/get_html/helps.php:19-45`

```php
function print_data($revision, $HTML_text, $sourcelanguage, $title, $error = "")
{
    $jsonData = [...];
    echo json_encode($jsonData);
}
```

**Issue:** Direct output makes testing impossible and couples the function to HTTP context.
**Recommendation:** Return data, let controller handle output:

```php
function build_response(...): array {
    return $jsonData;
}

// In controller:
echo json_encode(build_response(...));
```

### 4.5 Missing Type Declarations

Most functions lack PHP 8.0+ type declarations:

```php
// Current
function get_revision($HTML_text)

// Recommended
function get_revision(string $HTML_text): string
```

### 4.6 God Function / Long Method

**File:** `public_html/TranslationMdwiki/index.php`

The file mixes PHP logic, HTML templates, and JavaScript. This violates separation of concerns.

**Recommendation:** Split into:
- `TranslationTestController.php` - Logic
- `translation-test.phtml` - Template
- `translation-test.js` - Client-side code

---

## 5. Missing Documentation

### Files Without Docstrings

All PHP files lack proper PHPDoc blocks:

- `get_html/index.php`
- `get_html/post.php`
- `get_html/m.php`
- `get_html/fixiit.php`
- `get_html/helps.php`
- `mdtexts/files.php`
- `mdtexts/segments.php`
- `mdwiki_api.php`
- `post.php`
- `db404.php`

### Required PHPDoc Format

```php
/**
 * Fetches HTML content from mdwiki.org REST API.
 *
 * @param string $title    The page title to fetch (URL-encoded if necessary)
 * @param string $revision Optional revision ID; if provided, fetches by revision
 * @param string $domain   Base domain for the API (default: https://mdwiki.org)
 *
 * @return string HTML content of the page, or empty string on failure
 *
 * @throws NetworkException When cURL request fails
 * @throws ApiException     When API returns an error response
 *
 * @example
 * $html = get_text_html("COVID-19", "");
 * $html = get_text_html("", "1420795");
 */
function get_text_html(string $title, string $revision, string $domain = "https://mdwiki.org"): string
```

---

## 6. Recommendations Summary

### Immediate Actions (Critical)

1. **Fix bug in `fixiit.php:77`** - Change `$error['error']` to `$fixed['error']`
2. **Fix path traversal in `mdtexts/segments.php`** - Add input validation
3. **Remove hardcoded credentials from `db404.php`** - Use environment variables

### Short-Term Actions (High Priority)

4. Add PHP 8.0+ type declarations to all functions
5. Add PHPDoc documentation to all functions
6. Fix header ordering in `get_html/index.php`
7. Consolidate duplicate cURL code into shared utility

### Long-Term Actions (Medium Priority)

8. Refactor DOM parsing to single-pass architecture
9. Implement proper dependency injection
10. Add comprehensive error handling with custom exceptions
11. Add caching headers to API responses
12. Separate concerns in `TranslationMdwiki/index.php`

---

## Appendix A: Type Definitions

### Recommended Type Aliases

```php
<?php
/**
 * Type definitions for the Medwiki application.
 */

namespace Medwiki\Types;

/**
 * Represents a successful API response for content segmentation.
 */
type SegmentedContentResponse = array{
    sourceLanguage: string,
    title: string,
    revision: string,
    segmentedContent: string,
    categories: array<string>,
    error?: string
};

/**
 * Response from the HtmltoSegments external service.
 */
type HtmlToSegmentsResponse = array{
    result?: string,
    error?: string
};

/**
 * Parsed MediaWiki REST API error response.
 */
type MediaWikiErrorResponse = array{
    errorKey: string,
    messageTranslations: array<string, string>,
    httpCode: int,
    httpReason: string
};
```

---

## Appendix B: Security Checklist

- [ ] Input validation on all user-supplied parameters
- [ ] Output encoding for all dynamic content
- [ ] Path canonicalization before file operations
- [ ] Secure cookie handling with proper paths
- [ ] Rate limiting on external API calls
- [ ] Request timeout enforcement
- [ ] SSL certificate verification (currently enabled)
- [ ] Secrets management (credentials, API keys)
- [ ] Error message sanitization (no stack traces in production)
