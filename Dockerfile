FROM php:8.4-fpm

# Install PostgreSQL extension
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Copy the application code
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install project dependencies
RUN composer install

# Copy existing application directory permission
COPY --chown=www-data:www-data . .

# Expose port 9000
EXPOSE 9000
