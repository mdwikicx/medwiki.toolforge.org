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

mkdir -p "public_html/new_html"

# Copy the required files to the target directory
# cp -rf "$CLONE_DIR"/public_html/new_html/* "public_html/new_html" -v

# Sync required files to the target directory
rsync -a --delete --info=stats1,progress2 "$CLONE_DIR/public_html/new_html/" "public_html/new_html/"

rm -rf "$CLONE_DIR"
