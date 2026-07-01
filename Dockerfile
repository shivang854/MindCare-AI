FROM php:8.4-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN a2enmod rewrite

COPY . /var/www/html/

WORKDIR /var/www/html

RUN if [ -f composer.json ]; then \
    php -r "copy('https://getcomposer.org/installer','composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    composer install --no-dev --optimize-autoloader; \
fi

EXPOSE 80