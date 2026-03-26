#!/bin/bash

if [ -f ".env" ] && [ -f "./frontend/.env" ] && [ -f "./backend/.env" ]; then
    echo "A .env fájlok már léteznek"
else
    [ -f ".env" ] || cp .env.example .env
    [ -f "./frontend/.env" ] || cp .env ./frontend/.env
    [ -f "./backend/.env" ] || cp .env ./backend/.env
fi

if ! docker volume inspect shared_npm >/dev/null 2>&1; then
  docker volume create shared_npm
fi

if ! docker volume inspect shared_composer >/dev/null 2>&1; then
  docker volume create shared_composer
fi

docker run --rm  -v "$(pwd)/frontend:/app" -v "shared_npm:/shared_npm/" --entrypoint npm brownbas/sveltekit install

docker compose up -d

docker compose exec backend composer install

docker compose exec backend php artisan migrate

if [ -z "$(grep 'APP_KEY=base64' ./backend/.env)" ]; then
    docker compose exec backend php artisan key:generate
else
    echo "Az API kulcs már létezik" 
fi
