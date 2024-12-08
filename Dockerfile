# Базовый образ
FROM php:8.2-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql

# Установка Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Установка рабочего каталога
WORKDIR /var/www/html

# Копирование файлов приложения
COPY . .

# Установка зависимостей приложения
RUN composer install --no-dev --optimize-autoloader

# Установка прав доступа
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Запуск php-fpm
CMD ["php", "artisan", "serve", "--host=127.0.0.1", "--port=8000"]

