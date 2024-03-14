#!/bin/bash

# Publish the assets of Laravel Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Publish the assets of L5 Swagger
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"

# Generate Swagger documentation
php artisan l5-swagger:generate

# Migrate and seed the database
php artisan migrate --seed
