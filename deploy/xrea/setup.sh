#!/bin/bash -x

cp .env.example .env
sed -i 's/DB_DATABASE=devcontainer/DB_DATABASE=bookkeeping/' .env
php artisan key:generate
php artisan migrate:fresh
