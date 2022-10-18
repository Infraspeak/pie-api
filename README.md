# pie-api
## PIE Intro
This is a service that belongs to a bigger project (PIE). PIE's objective is to return all issues from repositories present on a `composer.json` or a `package.json` file.
The idea came in Hactoberfest 2020, when the team wanted to contribute to the packages we are using, but there was no easy way to list them all.
PIE project is composed by 5 projects:
* [pie-api](https://github.com/Infraspeak/pie-api) - The api service, this project
* [pie-frontend](https://github.com/Infraspeak/pie-frontend) - The frontend service written in Vue.js
* [pie-composer-parser-service](https://github.com/Infraspeak/pie-composer-parser-service) - The service responsible for parsing `composer.json` files
* [pie-npm-parser-service](https://github.com/Infraspeak/pie-npm-parser-service) - The service responsible for parsing `package.json` files
* [pie-github-issue-finder-service](https://github.com/Infraspeak/pie-github-issue-finder-service) - The service responsible for finding package issues on Github

## Resume
This service receives a `composer.json` or a `package.json` via the API and injects it into the respective Redis queue.
It will listen on another Redis queue for issues and send them to Pusher to be displayed by the frontend.

## Project Setup
The following commands assume you have `.direnv` installed and authorized. Check how to do it [here](https://direnv.net/docs/installation.html)
```
docker-compose build
composer install
cp .env.example .env
php artisan key:generate
```
Add your Pusher key and secret to variables `PUSHER_APP_*` in your `.env` file.

## Project Run
`docker-compose up` or with `-d` flag to run in detached mode
and on another terminal
`php artisan redis:subscribe:issues`

## How it works
An endpoint is exposed in `POST /API/files` expecting a `composer.json` or a `package.json` file and a unique identifier (UUID).
Depending on the received file, the endpoint will inject the file content in one of two Redis queues, `COMPOSER_FILE` for `composer.json` and `NPM_FILE` for `package.json` ex:
```json
{
   "headers": {
      // uuid identifier
   },
   "payload": {
      // composer.json or package.json file content
   }
}
```

Both queues are being listened to by other services that will process them. The UUID will also be propagated, as it identifies the Pusher WebSocket that should receive the parsed files later on.

A command, `PHP artisan redis:subscribe:issues` will be listening to the `ISSUES` Redis queue that should receive messages in the following format:

```json
{
   "headers":{
      // uuid identifier
   },
   "payload":{
      "repo":{
         "name":"vendor/package",
         "url":"repo url"
      },
      "issues":[
         {
            "url":"",
            "title":"",
            "description":"",
            "author":"",
            "status":"open/closed",
            "tags":[
               "hacktoberfest"
            ],
            "id":"",
            "date_opened":""
         }
      ]
   }
}
```

A command, `PHP artisan redis:subscribe:errors` will be listening to the `APP_ERRORS` Redis queue that should receive messages with errors in the following format:

```json
{
   "headers":{
      // uuid identifier
   },
   "payload":{
      "errors": [
         {
            "code":"",
            "description":""
         }
      ]
   }
}
```
