# =====================================================================
#  Aplikasi Koperasi (CodeIgniter 4) — Image PHP 8.2 + Apache
# =====================================================================
FROM php:8.2-apache

# ---- Dependensi sistem untuk ekstensi PHP ----
RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev \
        libonig-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        unzip \
        default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        mbstring \
        mysqli \
        pdo_mysql \
        gd \
        zip \
        exif \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ---- Konfigurasi Apache: DocumentRoot ke /public + mod_rewrite ----
RUN a2enmod rewrite headers
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# ---- Tuning PHP untuk aplikasi (upload backup, dll) ----
RUN { \
        echo 'upload_max_filesize=64M'; \
        echo 'post_max_size=64M'; \
        echo 'memory_limit=256M'; \
        echo 'max_execution_time=120'; \
    } > /usr/local/etc/php/conf.d/koperasi.ini

# ---- Composer ----
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ---- Install dependency PHP (cache layer) ----
COPY composer.json ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --no-progress \
    || composer update --no-dev --no-scripts --no-interaction --prefer-dist --no-progress

# ---- Salin seluruh source aplikasi ----
COPY . .

# ---- Hak akses folder writable ----
RUN chown -R www-data:www-data /var/www/html/writable /var/www/html/public/uploads 2>/dev/null || true \
    && chmod -R 775 /var/www/html/writable

# ---- Entrypoint: generate .env, tunggu DB, migrate, seed, start Apache ----
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
