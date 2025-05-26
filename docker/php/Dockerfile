FROM php:8.3-fpm-alpine
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod uga+x /usr/local/bin/install-php-extensions && sync
# Install dependencies
RUN install-php-extensions \
bcmath \
pdo_mysql \
gd \
exif \
redis \
pcntl \
zip \
intl
# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Set working directory inside the container
WORKDIR /var/www/html

# Copy your application code into the image
COPY src /var/www/html/

# Set correct permissions for storage and bootstrap/cache (inside the image)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run Composer install/autoloader optimization AFTER copying the code
# This command uses --no-dev for production and --optimize-autoloader for performance
RUN composer install --optimize-autoloader --no-interaction
CMD ["php-fpm"]
# Expose port 9000 and start php-fpm server
EXPOSE 9000