# 1. Gunakan image PHP 8.4 yang stabil
FROM serversideup/php:8.4-fpm-nginx

# 2. Set working directory di dalam server
WORKDIR /var/www/html

# 3. Salin seluruh proyek Laravel dari Windows ke dalam server Render
COPY --chown=webuser:webuser . .

# 4. PERBAIKAN UTAMA: Berikan izin akses penuh (Writable) pada folder krusial Laravel
# Perintah ini dijalankan sebagai USER root sementara untuk mengubah izin Linux
USER root
RUN mkdir -p storage/logs bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache && \
    chown -R webuser:webuser storage bootstrap/cache

# Kembalikan ke user biasa sebelum menjalankan composer
USER webuser

# 5. Jalankan optimasi sistem Laravel internal secara otomatis saat production
RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=php && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# 6. Buka port jaringan default Render
EXPOSE 8080