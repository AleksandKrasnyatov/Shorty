FROM php:8.3-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Установка PHP-расширений (только нужные)
RUN docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    opcache \
    sockets

# Установка Redis через PECL (этот работает)
RUN pecl install redis-5.3.7 && docker-php-ext-enable redis

# Установка Composer
COPY --from=composer:2.7.7 /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www

# Переносим composer.json и устанавливаем зависимости
COPY ../composer.json composer.json
RUN composer install --optimize-autoloader

# Копируем код
COPY .. .

# Права
RUN chown -R www-data:www-data /var/www

# Запуск
CMD ["php-fpm"]