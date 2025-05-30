# Use PHP-FPM for fast CGI
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

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy your application files into the container
COPY ./src /var/www/html

# Set the working directory
WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Expose port 9000 for php-fpm
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]