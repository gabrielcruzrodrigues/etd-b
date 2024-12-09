#!/bin/bash

cp .env.example .env

# Espera o serviço MySQL estar disponível
while ! nc -z mysql_db 3306; do
  sleep 1
done

sleep 10

composer install
php artisan migrate --seed
php artisan key:generate
php artisan serve --host=0.0.0.0 --port=9000
