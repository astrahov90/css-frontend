FROM yiisoftware/yii2-php:8.1-apache
WORKDIR /var/www/html/

COPY ./ .
RUN composer install
RUN composer dump-autoload
RUN a2enmod rewrite
RUN php init.php prepareSQLite
RUN chmod 777 ./app/db/
RUN chmod 777 ./app/db/sqlite.db
RUN php init.php prepareSampleData

RUN sed -i -e 's|ssl:warn|ssl:warn AllowOverride All|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i -e 's|/app/web|/var/www/html|g' /etc/apache2/sites-available/000-default.conf