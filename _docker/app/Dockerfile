FROM php:8.3-fpm

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    git \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libmemcached-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mbstring pdo pdo_mysql zip sockets

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создаём рабочую директорию
WORKDIR /var/www/html

# Копируем файлы проекта
COPY . .

# Устанавливаем зависимости
RUN composer install --no-dev --optimize-autoloader || true

# Копируем конфигурации PHP
COPY _docker/app/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY _docker/app/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

CMD ["php-fpm"]