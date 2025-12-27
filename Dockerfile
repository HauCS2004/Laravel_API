# ============================================
# GIAI ĐOẠN 1: Dùng Node chuẩn (Không dùng Alpine)
# ============================================
FROM node:18 as node_builder

WORKDIR /app

# Copy file config. 
# Mẹo: Copy package.json trước để tận dụng cache
COPY package.json vite.config.js ./

# Nếu có package-lock.json thì copy, không có thì thôi (tránh lỗi not found)
COPY package-lock.json* ./

# Cài đặt dependency (Dùng --legacy-peer-deps để tránh lỗi xung đột version nếu có)
RUN npm install --legacy-peer-deps

# Copy toàn bộ code
COPY . .

# Build với RAM cao
RUN NODE_OPTIONS="--max-old-space-size=4096" npm run build


# ============================================
# GIAI ĐOẠN 2: PHP Laravel (Giữ nguyên như cũ)
# ============================================
FROM php:8.2

# Cài thư viện hệ thống
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip default-mysql-client ca-certificates

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN update-ca-certificates

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Copy thư mục build từ Giai đoạn 1 sang
COPY --from=node_builder /app/public/build /var/www/public/build

RUN composer install --no-dev --optimize-autoloader
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000
