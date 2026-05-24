# ==========================================
# STAGE 1: Kompilasi Aset Frontend (Vite)
# ==========================================
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json vite.config.js tailwind.config.js ./
# Salin folder resources karena di sana ada file CSS dan JS mentah kamu
COPY resources/ ./resources/
COPY public/ ./public/
RUN npm install && npm run build

# ==========================================
# STAGE 2: Merakit Server PHP & Laravel
# ==========================================
FROM serversideup/php:8.4-fpm-nginx

# Set working directory di dalam server web
WORKDIR /var/www/html

# Salin seluruh proyek Laravel dari Windows ke dalam server
COPY --chown=www-data:www-data . .

# PENTING: Ambil hasil kompilasi folder "build" dari STAGE 1 ke mari
COPY --from=frontend-builder --chown=www-data:www-data /app/public/build ./public/build

# Berikan izin akses menggunakan user root
USER root
RUN mkdir -p storage/logs bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache

# Kembalikan ke user biasa bawaan image untuk keamanan eksekusi Composer
USER www-data

# Jalankan optimasi sistem Laravel internal produksi
RUN rm -rf bootstrap/cache/*.php && \
    composer install --no-dev --optimize-autoloader --ignore-platform-req=php && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Buka port jaringan default Render
EXPOSE 8080