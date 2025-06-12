# RAMJACK

RAMJACK is a web application built using Laravel 12 and Filament.

## Requirements

-   PHP >= 8.2
-   Composer
-   PHP GD

<details>
<summary>
Installation & Setup For Git Clone Codebase
</summary>

1. Install PHP, Composer, and PHP GD

    Make sure PHP 8.2+, Composer and PHP GD are installed on your system.

2. Clone the repository
    ```bash
    git clone https://github.com/FederalNexusInc/laravel-docker.git
    cd laravel-docker
    ```

3. Install dependencies
    ```bash
    composer install
    ```

4. Create environment file

    Copy the sample environment file and configure it as needed.

    ```bash
    cp .env.sample .env
    ```

5. Generate application key

    ```bash
    php artisan key:generate
    ```

6. Run database migrations and Seed the database

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

7. Set up Filament Shield

    Generate necessary permissions and assign a super admin:

    ```bash
    php artisan shield:generate --all
    php artisan shield:super-admin
    ```

8. Start the development server

    ```bash
    php artisan serve
    ```

    The application will be available at: http://localhost:8000
</details>


<details>
<summary>
Installation & Setup For Docker Container
</summary>

1. Clone the repository
    ```bash
    git clone https://github.com/FederalNexusInc/laravel-docker.git
    cd laravel-docker
    ```

2. Install dependencies
    ```bash
    composer install

3. Run Docker Compose
    ```yml
    services:
        database:
            restart: unless-stopped
            image: mysql
            ports:
            - 3306:3306
            environment:
            - MYSQL_ROOT_PASSWORD=passwordHere
            volumes:
            - mysql-persistent:/var/lib/mysql
            networks:
            - ramJackApp

        php:
            build: .
            expose:
            - 9000
            container_name: php_container
            volumes:
            - ./src:/var/www/html
            - yourENVFilePathHere:/var/www/html/.env
            networks:
            - ramJackApp
            command: >
            sh -c "chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache &&
            chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache &&
            php-fpm"

        caddy:
            image: caddy:latest
            volumes:
            - yorCaddyfileHere:/etc/caddy/Caddyfile
            - ./src:/var/www/html
            ports:
            - 5001:80
            depends_on:
            - php
            networks:
            - ramJackApp

        store:
            restart: unless-stopped
            image: redis:alpine
            volumes:
            - redis-persistent:/data
            networks:
            - ramJackApp

        volumes:
        redis-persistent:
        mysql-persistent:

        networks:
        ramJackApp:
            driver: bridge
    ```

4. Access The PHP Container
    ```bash
    docker exec -it php_container sh
    ```

5. Run The Setup Script
    ```bash
    ./setup.sh
    ```
    
### NOTE:
If running on local use the instructions above and comment out lines 25-27 src/app/Providers/AppServiceProvider.php. 

If deploying then leave src/app/Providers/AppServiceProvider.php as is and change the php service out for:
```yml
php:
    image: ghcr.io/federalnexusinc/laravel-docker:latest
    expose:
      - 9000
    container_name: php_container
    volumes:
      - src:/var/www/html
      - /var/lib/docker/volumes/ramJackApp/_data/.env:/var/www/html/.env
    networks:
      - ramJackApp
    command: >
      sh -c "chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache &&
      chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache &&
      php-fpm"
```
</details>