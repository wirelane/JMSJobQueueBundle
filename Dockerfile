FROM php:8.2-cli

# Basic build tools & composer
RUN apt-get update && apt-get install -y \
        git unzip libzip-dev libicu-dev libpq-dev libxml2-dev \
    && docker-php-ext-install intl zip pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app
COPY . /app