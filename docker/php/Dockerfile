FROM php:8.2-fpm

# install system dependencies
RUN apt-get update && apt-get install -y \
    imagemagick libmagickwand-dev git unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev \
    && pecl install imagick \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# set working directory
WORKDIR /var/www/html

# copy entrypoint
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

# install dependencies 
CMD ["php-fpm"]