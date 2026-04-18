FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    unzip \
    libzip-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql zip

RUN npm install -g sass

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN mkdir -p public/uploads views/templates_c logs && \
    chown -R www-data:www-data public/uploads views/templates_c logs && \
    chmod -R 777 public/uploads views/templates_c logs

COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 9000