FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

RUN curl -sS https://getcomposer.org/installer | php -- --version=2.4.1 && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/post-service

COPY . /var/www/post-service

COPY --chown=www-data:www-data . /var/www/post-service

RUN chmod -R 775 /var/www/post-service/storage
RUN chmod -R 775 /var/www/post-service/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
