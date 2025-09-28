#!/usr/bin/env bash

cd public_html/mediawiki

php maintenance/run.php importDump.php <  ~/public_html/Translation_Tool-20250920013003.xml
php maintenance/run.php rebuildrecentchanges.php
php maintenance/run.php initSiteStats.php
php maintenance/run.phpinitSiteStats.php --update
php maintenance/run.php
