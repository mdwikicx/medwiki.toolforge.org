# Medwiki Processing System

## Overview
This project is a web-based application that processes and converts WikiText for use within the Medwiki platform on toolforge.org. It is implemented in PHP and provides multiple ways to process Wiki content. The system consists of various endpoints and modules responsible for parsing, normalizing, and transforming Wiki markup into HTML output.

## How It Works

### 1. `get_html`
The `get_html` directory provides endpoints for generating HTML from WikiText in real-time. It includes:
- `index.php` – Main entry point for HTML generation.
- `post.php` – Handles POST requests for processing WikiText.
- `helps.php` – Provides documentation or support-related content.

These scripts take in WikiText, process it, and return the resulting HTML output.

### 2. `new_html`
The `new_html` directory represents a refined or alternate approach for processing WikiText. It is structured as follows:
- **`WikiText` module** – Contains the core parsing logic.
  - `WikiParse/` – Handles advanced parsing operations.
    - `Category.php` – Parses and structures category elements.
    - `Citations.php` – Manages citation-related parsing.
    - `Template.php` – Processes WikiText templates.
  - `fixes/` – Collection of scripts for fixing and normalizing WikiText.
    - `fix_cats.php` – Adjusts category structures.
    - `del_mt_refs.php` – Cleans up unnecessary reference elements.
  - `fix_wikitext.php` – General script for normalizing WikiText.

This module provides more modular and structured parsing capabilities compared to `get_html`.

### 3. `mdtexts`
The `mdtexts` directory likely handles markdown or alternate content formats. It serves as an additional processing layer for content that may not follow traditional WikiText structures but still needs transformation.

## Additional Components

### Background Processing (`jobs`)
- The `jobs` directory contains scripts for scheduled and administrative tasks.
- Example: `db_backup.sh` – Handles database backups and maintenance tasks.

### CI/CD & Automation (`.github/workflows`)
- Automated processes for documentation generation, testing, and deployment are managed through workflows.
- Example: `.github/workflows/snorkell-auto-documentation.yml` – Automates documentation updates.

### Configuration & Deployment
- `.coderabbit.yaml` – Configuration file for automated code analysis or testing.
- `medwiki-jobs.yaml` – Likely defines scheduled jobs and their execution details.

## System Architecture Mapping
1. **Web Server / Client Interface**
   - `public_html/` – Main entry point for handling requests.
2. **GET_HTML Endpoints**
   - `public_html/get_html/` – Provides immediate HTML conversion from WikiText.
3. **New HTML Processing (Refined WikiText Engine)**
   - `public_html/new_html/` – Contains a structured and modularized processing system.
4. **WikiText Parsing Module**
   - `public_html/new_html/WikiText/WikiParse/`
5. **WikiText Fixers**
   - `public_html/new_html/WikiText/fixes/`
   - `public_html/new_html/WikiText/fix_wikitext.php`
6. **Markdown / Alternate Content Processing**
   - `public_html/mdtexts/`
7. **Legacy / Static Content & Release Notes**
   - `public_html/w/`
8. **Background Processing**
   - `jobs/`
9. **CI/CD & Automated Documentation**
   - `.github/workflows/`
10. **Deployment & Configuration**
   - `.coderabbit.yaml`, `medwiki-jobs.yaml`

This README provides an overview of how the system processes WikiText and organizes various components for efficient operation.
