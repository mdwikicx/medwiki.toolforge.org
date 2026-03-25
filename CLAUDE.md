# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Medwiki is a PHP-based web application hosted on [Toolforge](https://medwiki.toolforge.org) that processes WikiText from mdwiki.org and converts it to HTML for the ContentTranslation tool. The application provides segmented HTML content that can be consumed by translation systems.

## Architecture

### Core Flow

```
mdwiki.org REST API -> get_html/index.php -> HTML Processing -> Segmented Content -> ContentTranslation tool
```

### Key Components

**`public_html/get_html/`** - Main HTML processing API:
- `index.php` - Entry point for HTML generation API
- `post.php` - HTTP client functions for fetching content from mdwiki.org REST API
- `m.php` - DOM manipulation functions: section extraction, link fixing, template removal
- `fixiit.php` - Integrates with external HtmltoSegments service for segmentation
- `helps.php` - Utility functions: revision extraction, JSON output formatting

**`public_html/mdtexts/`** - Alternative content delivery:
- `segments.php` - Serves pre-generated segmented content from files
- `files.php` - Lists available segment files
- Segment files are generated externally by mdwiki-python-files scripts

**`public_html/mdwiki_api.php`** - API proxy that forwards requests to mdwiki.org API with optional wmcloud target

### External Dependencies

- **HtmltoSegments service** (`https://ncc2c.toolforge.org/HtmltoSegments`) - External API for HTML segmentation
- **mdwiki.org REST API** - Source of wiki content (`/w/rest.php/v1/page/{title}/html`)

### Data Flow

1. API receives `title` or `revision` parameter
2. Fetches HTML from mdwiki.org REST API
3. Applies DOM transformations (remove templates, fix links, extract sections)
4. Optionally segments HTML via HtmltoSegments service
5. Caches processed content in `get_html/revisions/{revision}.html`
6. Returns JSON with `segmentedContent`, `revision`, `title`, `sourceLanguage`

## Deployment

Hosted on Wikimedia Toolforge. Deployment is automated via GitHub Actions:

- Push to `main` branch triggers `.github/workflows/update.yaml`
- Runs `shs/update_html.sh` on the Toolforge server
- Script clones repo, removes unnecessary files, and deploys to `public_html/`

### Manual Deployment on Toolforge

```bash
# SSH into Toolforge and run:
shs/update_html.sh [branch_name]  # defaults to main
```

## Background Jobs

- **Database backup**: `jobs/db_backup.sh` runs daily at 01:11 UTC via `medwiki-jobs.yaml`
- Database name: `s55992__wiki_cx`
- Backups stored in `databasebackup/`

## API Endpoints

| Endpoint | Parameters | Description |
|----------|------------|-------------|
| `/get_html/` | `title`, `revision`, `section0`, `all`, `nofix`, `printetxt`, `rmstyle`, `sourcelanguage`, `test` | Generate segmented HTML from mdwiki.org |
| `/mdtexts/segments.php` | `title` | Retrieve pre-generated segment file |
| `/mdtexts/files.php` | - | List available segment files |
| `/mdwiki_api.php` | Any api.php params, `wmcloud` | Proxy to mdwiki.org API |

## Important Notes

- The `revisions/` directory caches processed HTML by revision ID to avoid reprocessing
- `mdtexts/segments/` and `mdtexts/html/` directories are populated by external Python scripts
- The codebase uses PHP's DOMDocument for HTML parsing and manipulation
- User agent for external requests: `WikiProjectMed Translation Dashboard/1.0 (https://medwiki.toolforge.org/; tools.medwiki@toolforge.org)`
