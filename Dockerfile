FROM yiisoftware/yii2-php:8.1-apache
WORKDIR /var/www/html/

COPY ./ .
RUN touch .env
RUN echo 'REDIS_HOST=redis-server' >> .env
RUN pecl install redis && docker-php-ext-enable redis
RUN composer install
RUN composer dump-autoload
RUN a2enmod rewrite
RUN chmod 777 ./app/views/cache/
RUN php init.php prepareSQLite
RUN chmod 777 ./app/db/
RUN chmod 777 ./app/db/sqlite.db
RUN php init.php prepareSampleData

RUN sed -i -e 's|ssl:warn|ssl:warn AllowOverride All|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i -e 's|/app/web|/var/www/html|g' /etc/apache2/sites-available/000-default.conf