FROM php:8.1-apache


RUN apt-get update && apt-get install -y --fix-missing \
    curl \
    git \
    unzip \
    libzip-dev \
    iputils-ping \
    && docker-php-ext-install zip mysqli


RUN a2enmod rewrite


RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN echo "<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" >> /etc/apache2/apache2.conf


WORKDIR /var/www/html


COPY . .


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json /var/www/html/
RUN composer install --no-dev --optimize-autoloader


RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html


EXPOSE 80


CMD ["apache2-foreground"]