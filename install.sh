# https://www.mediawiki.org/wiki/Download_from_Git#Fetch_external_libraries

git clone https://gerrit.wikimedia.org/r/mediawiki/core.git mediawiki

git fetch --tags
git checkout 1.44.0


zip -r mediawiki.zip mediawiki -9 -x "*/.git/*" "*/i18n/*" "*/tests/*"
