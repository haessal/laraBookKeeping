#!/bin/bash -e
# Initialize development environment with Laravel Sail

export DOCKER_CLI_HINTS=false

ENV_FILE=.env
IMAGE=laravelinstaller:devcontainer

if [ -f "${ENV_FILE}" ]; then
    echo "Doing nothing because ${ENV_FILE} file already exists."
    exit 0
fi
if [ ! -f composer.lock ]; then
    echo "Doing nothing because no composer.lock found."
    exit -1
fi
if [ ! -f package-lock.json ]; then
    echo "Doing nothing because no package-lock.json found."
    exit -1
fi

set -x
# Build laravel installer container if not exist
docker build .devcontainer/workspace/ -t ${IMAGE}

# Install composer package include Laravel framework and Sail
docker run --rm -it -v $(pwd):/workspace -w /workspace ${IMAGE} composer install

# Create .env to run Sail
cp .env.example ${ENV_FILE}

# Run Sail
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
done
set -x

# Install node pacakges
./vendor/bin/sail npm ci

# Generate APP_KEY
./vendor/bin/sail artisan key:generate

# Migrate datebase
./vendor/bin/sail artisan migrate:fresh --seed

# Build asset
./vendor/bin/sail npm run build
set +x

echo "Complete succesfuly. Try to access http://localhost"
