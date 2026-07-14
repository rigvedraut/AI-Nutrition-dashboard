FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install system dependencies needed by mPDF (gd, zip, mbstring)
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libpng-dev \
    && docker-php-ext-install gd zip mbstring \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files into Apache's web root
COPY . /var/www/html/

WORKDIR /var/www/html

# Install PHP dependencies (mPDF etc.)
RUN composer install --no-dev --optimize-autoloader

# Apache listens on port 80 by default; Render maps this automatically
EXPOSE 80