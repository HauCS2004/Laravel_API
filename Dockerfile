# 1. Dùng PHP 8.2 (hoặc ver bạn đang dùng)
FROM php:8.2-fpm

# 2. Cài đặt các thư viện cần thiết cho Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# 3. Cài các extension PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 4. Cài Composer (Trình quản lý thư viện PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Thiết lập thư mục làm việc
WORKDIR /var/www