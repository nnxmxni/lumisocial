#!/usr/bin/env bash

if [ ! -f ".env" ]; then
    echo ".env does not exist. creating one"
    cp .env.example .env
else
    echo ".env alread exists. skipping creation"
fi

role=${CONTAINER_ROLE:-app}

if [ "$role" = "app" ]; then
    echo "running the app container"
    php artisan serve --port="$PORT" --host=0.0.0.0 --env=.env
else
    echo "could not match container role \"$role\""
    exit 1
fi
