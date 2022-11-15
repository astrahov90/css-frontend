FROM php:8.1-apache

WORKDIR /var/www/html/
COPY . ./

RUN a2enmod rewrite
RUN php init.php prepareSQLite
RUN php init.php prepareSampleData

RUN sed -i -e 's|ssl:warn|ssl:warn AllowOverride All|g' /etc/apache2/sites-available/000-default.conf
