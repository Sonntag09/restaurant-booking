Run:
docker-compose up -d
docker-compose exec app bash
php artisan migrate:fresh --seed
