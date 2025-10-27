FROM php:8.2-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo pdo_mysql gd zip opcache

# Установка composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www/html

# Права на запись (для runtime, assets, uploads)
RUN mkdir -p /var/www/html/web/uploads /var/www/html/runtime /var/www/html/web/assets && \
    chmod -R 777 /var/www/html/runtime /var/www/html/web/assets /var/www/html/web/uploads

CMD ["php-fpm"]
