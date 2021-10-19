# Lumen
## Description
This project receives a `composer.json` or a `package.json` via the API, and injects it into the respective Redis queue.
It will listen on another Redis queue for issues and send them to Pusher

## Project Setup
```
docker-compose build
composer install
cp .env.example .env
```
Add your Pusher key and secret to variables `PUSHER_APP_*` in your `.env` file

## Project Run
`docker-compose up`
and on another terminal
`php artisan redis:subscribe:issues`
