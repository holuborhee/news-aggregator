Generate API KEY on News API

After docker compose is up, run this

`docker exec -it laravel_app php artisan key:generate`
`docker exec -it laravel_app php artisan migrate --seed --force`
`docker exec -it laravel_app php artisan app:setup-news-aggregator`

<!-- `docker exec -it laravel_app php artisan schedule:work` -->

Set `QUEUE_CONNECTION` to `sync`

# Generate app key

docker exec -it laravel_app php artisan key:generate

# Run migrations

docker exec -it laravel_app php artisan migrate

# Seed categories or any other seeders

docker exec -it laravel_app php artisan db:seed

docker exec -it laravel_app php artisan app:setup-news-aggregator

### Things to look into

- The placement of the migration command can be better to ensure availability of the database when migration is about to run
- Improve docker-compose and dockerfile to handle both production and dev commands
- May change URL from /everything /top-headlines
- Debug the no scheduled command is ready to run
- make scheduled frequency to be easily configured
