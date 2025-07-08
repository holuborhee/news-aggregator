After docker compose is up, run this

`docker exec -it laravel_app php artisan key:generate`
`docker exec -it laravel_app php artisan migrate --seed --force`

The placement of the migration command can be better to ensure availability of the database when migration is about to run

Improve docker-compose and dockerfile to handle both production and dev commands
