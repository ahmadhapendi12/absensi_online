FROM php:8.4-fpm

# Ganti mirror Debian ke mirror lokal Indonesia (DataUtama Surabaya) agar koneksi super cepat & anti-timeout
RUN sed -i 's|deb.debian.org|kartolo.sby.datautama.net.id|g' /etc/apt/sources.list.d/debian.sources 2>/dev/null || true \
    && sed -i 's|deb.debian.org|kartolo.sby.datautama.net.id|g' /etc/apt/sources.list 2>/dev/null || true

# Gunakan docker-php-extension-installer resmi agar instalasi ekstensi instan tanpa error TLS/compile
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install ekstensi PHP yang dibutuhkan Laravel 13, Filament 5, dan PostgreSQL
RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    gd \
    intl \
    zip \
    bcmath \
    exif \
    opcache

# Install tool pendukung yang dibutuhkan Composer & Git
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Salin binary Composer resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin source code aplikasi
COPY . .

# Set hak akses permission direktori storage & bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
