FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    postgresql-dev \
    rabbitmq-c-dev \
    make \
    g++

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql \
    && docker-php-ext-enable pdo pdo_pgsql \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application source code
COPY src /var/www/html

# Expose port
EXPOSE 9000

CMD ["php-fpm"]
