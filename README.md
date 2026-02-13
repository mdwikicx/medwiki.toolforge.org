# Medwiki Project

## Overview
The Medwiki project is a web-based application hosted on [Toolforge](https://medwiki.toolforge.org) that processes and converts WikiText into HTML. The core functionalities include parsing Wiki markup, normalizing content, and generating structured HTML outputs. The project is implemented in PHP and features multiple methods for handling WikiText transformation.

## File Structure
```
📂 Medwiki
├── 📂 .github
│   └── 📂 workflows
│       └── snorkell-auto-documentation.yml  # CI/CD automation for documentation
├── 📂 jobs
│   └── db_backup.sh  # Background job for database backups
├── 📂 public_html
│   ├── 📂 get_html  # API endpoints for HTML generation
│   │   ├── index.php
│   │   ├── post.php
│   │   ├── helps.php
│   ├── 📂 mdtexts  # Alternative content processing
│   ├── 📂 w  # Legacy/static content & release notes
├── .coderabbit.yaml  # Configuration management
├── medwiki-jobs.yaml  # Deployment and job configurations
└── .gitignore  # Environment configuration
```

## System Architecture
### 1. Web Server / Client Interface
- **Public HTML Directory**: Serves as the web root.
- **API Endpoints**:
  - `get_html`: Processes WikiText and converts it to HTML.
- **WikiText Processing Modules**:
  - `WikiParse`: Tokenization, template parsing.
  - `Fixers`: Scripts to sanitize and standardize WikiText.
- **Additional Content Handling**:
  - `mdtexts`: Alternative formats.
  - `w`: Static/legacy content storage.

### 2. Background Jobs
- **db_backup.sh**: This script automates database backups to ensure data consistency and recovery in case of failures. It is scheduled to run periodically and stores backups securely.

### 3. CI/CD & Automation
- **snorkell-auto-documentation.yml**: Automates documentation updates by generating or updating README and other relevant docs whenever code changes are pushed.
- **.coderabbit.yaml**: Defines automated testing and deployment workflows for continuous integration.
- **medwiki-jobs.yaml**: Configures various deployment tasks, including job scheduling and infrastructure setup on Toolforge.

## Deployment & External Integrations
- Hosted on [Toolforge](https://medwiki.toolforge.org).
- Uses YAML configurations for job scheduling and deployment.
- CI/CD integration for automated documentation and code testing.

## Contributing
1. Clone the repository:
   ```sh
   git clone https://github.com/your-repo/medwiki.git
   ```
2. Make necessary changes and test locally.
3. Push your changes and create a pull request.

## License
This project is licensed under the [MIT License](LICENSE).
