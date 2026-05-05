#!/bin/bash -e

function overwrite_env_file() {
    local env_file=$1
    local mysql_database=${MYSQL_DATABASE:-bookkeeping}
    local mysql_user=${MYSQL_USER:-default}
    local mysql_password=${MYSQL_PASSWORD:-secret}
    local items=(
        "DB_CONNECTION=mysql"
        "DB_HOST=db"
        "DB_PORT=3306"
        "DB_DATABASE=${mysql_database}"
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

echo "Set up .env"
cp .env.example .env
overwrite_env_file .env

echo "Generating key"
php artisan key:generate

echo "Set up database"
php artisan migrate:fresh
