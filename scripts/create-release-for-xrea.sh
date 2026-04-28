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

cd ..

# Create release archive
echo "Creating archive: ${ARCHIVE_NAME}"
tar -czf "${ARCHIVE_NAME}" \
    --exclude='book-keeping/.git' \
    --exclude='book-keeping/.env' \
    --exclude='book-keeping/node_modules' \
    --exclude='book-keeping/vendor' \
    --exclude='book-keeping/tests' \
    --exclude='book-keeping/phpunit.xml' \
    book-keeping/

mv "${ARCHIVE_NAME}" ./deploy/xrea/

echo "Done: deploy/xrea/${ARCHIVE_NAME}"
