# Use ARG to accept PHP version from docker-compose
ARG PHP_VERSION=7.4
FROM php:${PHP_VERSION}-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zlib1g-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    redis-tools \
    default-mysql-client

# Install PHP extensions
RUN docker-php-ext-install \
    pcntl \
    mysqli \
    pdo_mysql \
    zip \
    bcmath \
    sockets

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Xdebug based on PHP version
RUN PHP_VERSION=$(php -r 'echo PHP_VERSION;') && \
    if [ "$(printf '%s\n' "8.0.0" "$PHP_VERSION" | sort -V | head -n1)" = "8.0.0" ]; then \
        pecl install xdebug && docker-php-ext-enable xdebug; \
    else \
        pecl install xdebug-3.1.5 && docker-php-ext-enable xdebug; \
    fi

# Enable Xdebug coverage
ENV XDEBUG_MODE=coverage

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install

# Expose ports for debugging (optional)
EXPOSE 9003

# Set default command
CMD ["php", "-a"]
