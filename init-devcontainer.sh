#!/bin/bash -e
# Initialize development environment with devcontainer.

ENV_FILE=.env
PNPM_VERSION=12.14.0

function overwrite_env_file() {
    local env_file=$1
    local mysql_user=${MYSQL_USER:-default}
    local mysql_password=${MYSQL_PASSWORD:-secret}
    local items=(
        "DB_HOST=db"
        "DB_DATABASE=devcontainer"
        "DB_USERNAME=${mysql_user}"
        "DB_PASSWORD=${mysql_password}"
        "SESSION_DRIVER=file"
        "MAIL_HOST=mailhog"
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

# Install Composer packages
echo "Installing dependencies using composer"
composer install

# Install Node.js packages using pnpm
echo "Installing Node.js dependencies"
corepack "pnpm@${PNPM_VERSION}" --version
corepack "pnpm@${PNPM_VERSION}" install --frozen-lockfile

echo "Creating .env file from .env.example"
cp .env.example ${ENV_FILE}
overwrite_env_file ${ENV_FILE}

echo "Generating application key"
php artisan key:generate

echo "Running database migrations"
php artisan migrate:fresh --seed

# Build assets
corepack "pnpm@${PNPM_VERSION}" run build

echo "Completed successfully"
