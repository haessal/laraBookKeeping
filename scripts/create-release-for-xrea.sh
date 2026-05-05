#!/bin/bash
set -e

# change directory to laravel application root directory
cd "$(dirname "$0")/../book-keeping"

VERSION=${1:-"dev"}
ARCHIVE_NAME="laraBookKeeping-${VERSION}.tar.gz"

echo "Building release: ${VERSION}"

# Build assets
echo "Building assets..."
npm run build

# Generate code reference
echo "Generating code reference..."
rm -Rf docs
rm -Rf .phpdoc
phpdoc -d ./app -t ./docs --ignore="vendor/*,node_modules/*,tests/*"

cd ..

# Create release archive
echo "Creating archive: ${ARCHIVE_NAME}"
tar -czf "${ARCHIVE_NAME}" \
    --exclude='book-keeping/.DS_Store' \
    --exclude='book-keeping/.env' \
    --exclude='book-keeping/.gitattributes' \
    --exclude='book-keeping/.gitignore' \
    --exclude='book-keeping/.phpdoc' \
    --exclude='book-keeping/.phpunit.result.cache' \
    --exclude='book-keeping/.phpunit.cache' \
    --exclude='book-keeping/.prettierignore' \
    --exclude='book-keeping/.prettierrc' \
    --exclude='book-keeping/node_modules' \
    --exclude='book-keeping/phpstan.neon' \
    --exclude='book-keeping/phpunit.xml' \
    --exclude='book-keeping/tests' \
    --exclude='book-keeping/vendor' \
    book-keeping/

mv "${ARCHIVE_NAME}" ./deploy/xrea/

echo "Done: deploy/xrea/${ARCHIVE_NAME}"
