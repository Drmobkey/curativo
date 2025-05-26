# Gunakan image PHP resmi + ekstensi Laravel
FROM php:8.2-fpm

# Install dependencies dasar
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    npm \
    nodejs \
    libzip-dev \
    vim \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set workdir
WORKDIR /var/www

# Copy file project Laravel ke dalam container
COPY . .

# Install dependency Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Install frontend (Breeze)
RUN npm install && npm run build

# Set permission
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Gunakan user www-data
USER www-data

CMD ["php-fpm"]
