# 1. Gunakan image PHP 8.4 yang stabil
FROM serversideup/php:8.4-fpm-nginx

# 2. Set working directory di dalam server
WORKDIR /var/www/html

# 3. PERBAIKAN 1: Ganti kepemilikan COPY ke user resmi www-data
COPY --chown=www-data:www-data . .

# 4. PERBAIKAN 2: Berikan izin akses menggunakan user www-data
USER root
RUN mkdir -p storage/logs bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache

# Kembalikan ke user biasa bawaan image untuk keamanan eksekusi Composer
USER www-data

# 5. Jalankan optimasi sistem Laravel internal secara otomatis saat production
RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=php && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# 6. Buka port jaringan default Render
EXPOSE 8080