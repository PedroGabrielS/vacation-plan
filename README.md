# Vacation Plan Project

## Introduction

This is a Laravel project designed to manage vacation plans. Below are the instructions for setting up the project and running it effectively.

## Setup Instructions

### Docker and Database Configuration

1. Run the following command to execute the Docker configuration:

    ```bash
    docker-compose up -d
    ```

2. Create the database using the following command:

    ```bash
    docker exec -it vacation-plan-postgres-1 psql -U root -c 
    ```
    
    After accessing the container, execute the command to create the table
     ```bash
    "CREATE DATABASE vacation;"
    ```
    

### Project Dependencies

- Laravel Framework: v10.48.1
- PHP: v8.1.10

Ensure the following extensions are uncommented in your `php.ini` file:

- `extension=pdo_pgsql`
- `extension=pdo_sqlite`

Run the following commands in the root directory of your project:

```bash
composer install
```
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

```bash
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
```

```bash
php artisan l5-swagger:generate
```

```bash
php artisan migrate --seed
```

### Running Tests

Execute the following command to run tests:

```bash
php artisan test
```

### Accessing API Documentation
Once the project is running, access the API documentation at the following route:

http://vacation-plan.test/api/documentation

### Test User for Generating Token
Use the test user credentials to generate the token required for testing:

Username: [test_username]

Password: [test_password]
