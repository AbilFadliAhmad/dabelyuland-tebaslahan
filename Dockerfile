# 1. Gunakan image PHP resmi yang sudah include dengan Nginx khusus Laravel
FROM serversideup/php:8.4-fpm-nginx

# 2. Set working directory di dalam server
WORKDIR /var/www/html

# 3. Salin seluruh source code Laravel dari Windows ke dalam server Render
COPY --chown=webuser:webuser . .

# 4. Jalankan optimasi sistem Laravel internal secara otomatis saat production
RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=php && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# 5. Buka port pengiriman jaringan default
EXPOSE 8080