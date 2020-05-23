FROM php:7.4-apache

RUN apt-get update \
	&& apt-get install -y cron

RUN echo "0 * * * * cd /var/www/html/cron && /usr/local/bin/php /var/www/html/cron/github.php" > /etc/cron.d/last-git-commit-cron
RUN chmod 644 /etc/cron.d/last-git-commit-cron
RUN crontab /etc/cron.d/last-git-commit-cron

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY . /var/www/html/
RUN chmod -Rv +rx .

WORKDIR /var/www/html/cron
RUN /usr/local/bin/php /var/www/html/cron/github.php
RUN chmod -Rv +rwx /var/www/html/cron/

WORKDIR /var/www/html

CMD cron -f & apache2-foreground
