#!/bin/bash
set -e

function overwrite_env_file() {
    local env_file=$1
    local mysql_user=${MYSQL_USER:-default}
    local mysql_password=${MYSQL_PASSWORD:-secret}
    local items=(
        "APP_URL=http://localhost:8000"
        "DB_CONNECTION=mysql"
        "DB_HOST=db"
        "DB_PORT=3306"
        "DB_DATABASE=devcontainer"
        "DB_USERNAME=${mysql_user}"
        "DB_PASSWORD=${mysql_password}"
        "SESSION_DRIVER=file"
    )

    while IFS= read -r line; do
        local key=$(echo "$line" | cut -d '=' -f 1)
        local value=$(echo "$line" | cut -d '=' -f 2-)
        if grep -q "^${key}=" "$env_file"; then
            sed -i "s|^${key}=.*|${key}=${value}|" "$env_file"
        else
            echo "${key}=${value}" >> "$env_file"
        fi
    done <<< "$(printf "%s\n" "${items[@]}")"
}

# Change directory to laravel application root directory
cd "$(dirname "$0")/../book-keeping"

ENV_FILE=.env

# Check if the .env file exists
if [ ! -f "${ENV_FILE}" ]; then
    echo "Installing dependencies using composer"
    composer install

    echo "Installing Node.js dependencies"
    npm install --legacy-peer-deps

    echo "Creating .env file from .env.example"
    cp .env.example ${ENV_FILE}
    overwrite_env_file ${ENV_FILE}

    echo "Generating application key"
    php artisan key:generate

    echo "Running database migrations"
    php artisan migrate:fresh
else
    echo "Doing nothing because .env file already exists."
fi
