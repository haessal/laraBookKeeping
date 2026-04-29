#!/bin/bash
set -e

# change directory to laravel application root directory
cd "$(dirname "$0")/../book-keeping"

# Check if the .env file exists
if [ ! -f .env ]; then
    echo "Installing dependencies using composer"
    composer install

    echo "Installing Node.js dependencies"
    npm install --legacy-peer-deps

    echo "Creating .env file from .env.example"
    cp .env.example .env

    echo "Generating application key"
    php artisan key:generate

    echo "Running database migrations"
    php artisan migrate:fresh
else
    echo "doing nothing because .env file already exists."
fi
