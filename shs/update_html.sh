#!/bin/bash

echo "Cd to home directory..."
cd "$HOME" || { echo "Failed to change directory to home directory"; exit 1; }

REPO_URL="https://github.com/mdwikicx/medwiki.toolforge.org.git"
TARGET_DIR="public_html"
CLONE_DIR="html_test"

# Remove any existing clone directory
rm -rf "$CLONE_DIR"

BRANCH="${1:-main}"
echo ">>> clone --branch ${BRANCH} ."

echo "Cloning repository from GitHub..."

if git clone --branch "$BRANCH" "$REPO_URL" "$CLONE_DIR"; then
    echo "Repository cloned successfully."
else
    echo "Failed to clone the repository." >&2
    exit 1
fi

rm -rf "$CLONE_DIR"/.git
rm -rf "$CLONE_DIR"/public_html/w
rm -rf "$CLONE_DIR"/public_html/get_html/revisions
rm -rf "$CLONE_DIR"/public_html/mdtexts/html
rm -rf "$CLONE_DIR"/public_html/mdtexts/segments
rm -rf "$CLONE_DIR"/public_html/mdtexts/wikitext
rm -rf "$CLONE_DIR"/public_html/mdtexts/*.json

# Copy the required files to the target directory
cp -rf "$CLONE_DIR"/public_html/* "$TARGET_DIR/" -v

# Sync required files to the target directory
# rsync -a --delete --info=stats1,progress2 "$CLONE_DIR/public_html/" "$TARGET_DIR/"

# Optional: Set permissions
# chmod -R 770 "$TARGET_DIR"
# find "$TARGET_DIR" -type f ! -name "*.pyc" -exec chmod 770 {} \;

# Optional: Install dependencies
#"$HOME/local/bin/python3" -m pip install -r "$TARGET_DIR/requirements.in"

# Remove the "$CLONE_DIR" directory.
rm -rf "$CLONE_DIR"
