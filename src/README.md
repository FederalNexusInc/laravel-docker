# RAMJACK

RAMJACK is a web application built using Laravel 12 and Filament.

## Requirements

-   PHP >= 8.2
-   Composer
-   PHP GD

## Installation & Setup

1. Install PHP, Composer, and PHP GD

    Make sure PHP 8.2+, Composer and PHP GD are installed on your system.

2. Clone the repository
    ```bash
    git clone https://github.com/tntData/filament-rjfs.git
    cd filament-rjfs
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
