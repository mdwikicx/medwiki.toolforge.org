# https://www.mediawiki.org/wiki/Download_from_Git#Fetch_external_libraries

git clone https://gerrit.wikimedia.org/r/mediawiki/core.git mediawiki

git fetch --tags
git checkout 1.44.0


zip -r mediawiki.zip mediawiki -9 -x "*/.git/*" "*/i18n/*" "*/tests/*"


# user
# password

cd extensions
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/BetaFeatures --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/DiscussionTools --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/Interwiki --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/Linter --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/MultimediaViewer --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/Nuke --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/PluggableAuth --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/TextExtracts --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/Thanks --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/UniversalLanguageSelector --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/UnlinkedWikibase --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/WSOAuth --branch REL1_44


git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/Cite --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/VisualEditor --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/ContentTranslation --branch REL1_44
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/WikimediaMessages --branch REL1_44

tfw php8.2 shell
cd public_html/mediawiki

composer update --no-dev

php maintenance/run.php update


zip -r -9 ~/cx_1.44.zip ~/public_html/mediawiki/extensions/ContentTranslation -x '*/.git/*' '*/i18n/*' '*/vendor/*'


composer require mediawiki/oauthclient
