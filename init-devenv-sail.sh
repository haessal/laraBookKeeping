#!/bin/bash -e
# Initialize development environment with Laravel Sail

export DOCKER_CLI_HINTS=false

ENV_FILE=.env
IMAGE=laravelinstaller:devcontainer
PNPM_VERSION=12.14.0

if [ -f "${ENV_FILE}" ]; then
    echo "Doing nothing because ${ENV_FILE} file already exists."
    exit 0
fi

if [ ! -f composer.lock ]; then
    echo "Doing nothing because no composer.lock found."
    exit 1
fi

if [ ! -f pnpm-lock.yaml ]; then
    echo "Doing nothing because no pnpm-lock.yaml found."
    exit 1
fi

set -x

# Build Laravel installer container if it does not exist
docker build .devcontainer/workspace/ -t "${IMAGE}"

# Install Composer packages
docker run --rm -it -v "$(pwd):/workspace" -w /workspace "${IMAGE}" composer install

# Create .env to run Sail
cp .env.example "${ENV_FILE}"

# Start Sail
./vendor/bin/sail down -v
./vendor/bin/sail up -d
set +x

until ./vendor/bin/sail exec mysql \
    mysqladmin ping \
    -h 127.0.0.1 \
    -u root \
    --silent
do
    sleep 1
    echo -n "."
done
echo ""

set -x

# Download and initialize pnpm through Corepack
./vendor/bin/sail exec laravel.test \
    corepack "pnpm@${PNPM_VERSION}" --version

# Install Node.js packages using pnpm
./vendor/bin/sail exec laravel.test \
    corepack "pnpm@${PNPM_VERSION}" install --frozen-lockfile

# Generate APP_KEY
./vendor/bin/sail artisan key:generate

# Migrate database
./vendor/bin/sail artisan migrate:fresh --seed

# Build assets
./vendor/bin/sail exec laravel.test \
    corepack "pnpm@${PNPM_VERSION}" run build

set +x

echo "Completed successfully. Try to access http://localhost:8000"
