FROM php:8.1-apache

# Cài đặt các thư viện hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    mariadb-client

# Cài đặt Node.js (dành cho Vite/Vue) bằng cách copy trực tiếp từ image chính thức để tránh lỗi mạng
COPY --from=node:20 /usr/local/bin/node /usr/local/bin/
COPY --from=node:20 /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Cài đặt các PHP extensions cần thiết cho Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip bcmath

# Bật Apache Mod Rewrite (quan trọng với routing của Laravel)
RUN a2enmod rewrite

# Đặt thư mục gốc cho Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Cài Nodejs + NPM
WORKDIR /var/www/html

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Cấu hình tối ưu hóa Docker Cache: Copy file cài đặt packages trước
COPY composer.json composer.lock package.json package-lock.json ./

# Cài đặt package của PHP & Node (Chỉ chạy lại khi các file Lock thay đổi)
RUN composer install --no-dev --no-scripts --no-autoloader
RUN npm install

# Copy toàn bộ mã nguồn dự án vào container
COPY . .

# Khởi tạo autoload cho Composer sau khi đã copy toàn bộ code
RUN composer dump-autoload --optimize

# Phân quyền cho storage và bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Build frontend (Vue/Vite)
RUN npm run build

EXPOSE 80

CMD ["apache2-foreground"]
