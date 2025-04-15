
FROM php:8.0-apache


RUN apt-get update && apt-get install -y --fix-missing \
    curl \
    git \
    unzip \
    libzip-dev \
    iputils-ping \
    && docker-php-ext-install zip mysqli


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN echo "<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" >> /etc/apache2/apache2.conf


RUN a2enmod rewrite

RUN sed -i 's|http://deb.debian.org|http://ftp.us.debian.org|g' /etc/apt/sources.list


WORKDIR /var/www/html

COPY composer.json /var/www/html/

RUN composer install


COPY . /var/www/html/


RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html


EXPOSE 80


CMD ["apache2-foreground"]