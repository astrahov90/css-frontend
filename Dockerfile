FROM yiisoftware/yii2-php:8.1-apache
WORKDIR /var/www/html/

COPY ./ .
RUN touch .env
RUN echo 'REDIS_HOST=redis-server' >> .env
RUN echo 'DB_TYPE=postgres' >> .env
RUN echo 'DB_HOST=postgres' >> .env
RUN echo 'DB_PORT=5432' >> .env
RUN echo 'DB_USER=postgres' >> .env
RUN echo 'DB_PASSWORD=12345678' >> .env
RUN echo 'DB_NAME=postgres' >> .env

RUN touch dbPrepare
RUN echo #!/bin/bash >> dbPrepare
RUN echo php init.php prepareTables >> dbPrepare
RUN echo php init.php prepareSampleData >> dbPrepare
RUN chmod +x ./dbPrepare

RUN pecl install redis && docker-php-ext-enable redis
RUN composer install
RUN composer dump-autoload
RUN a2enmod rewrite
RUN chmod 777 ./app/views/cache/
#RUN chmod 777 ./app/db/
#RUN chmod 777 ./app/db/sqlite.db
#RUN php init.php prepareTables
#RUN php init.php prepareSampleData

RUN sed -i -e 's|ssl:warn|ssl:warn AllowOverride All|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i -e 's|/app/web|/var/www/html|g' /etc/apache2/sites-available/000-default.conf