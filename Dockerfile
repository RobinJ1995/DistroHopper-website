FROM php:7.4-apache

RUN apt-get update \
	&& apt-get install -y cron libjpeg-dev libpng-dev libwebp-dev

# GD lets wallpaper.php derive the wallpaper's dominant colour, which the Unity
# theme uses to colour the launcher, dash and tiles. The wallpapers are WebP.
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
	&& docker-php-ext-install gd

RUN echo "0 * * * * cd /var/www/html/cron && /usr/local/bin/php /var/www/html/cron/github.php" > /etc/cron.d/last-git-commit-cron
RUN chmod 644 /etc/cron.d/last-git-commit-cron
RUN crontab /etc/cron.d/last-git-commit-cron

# This image's /etc/mime.types predates WebP, so Apache would serve the
# wallpapers with no Content-Type at all.
RUN echo 'AddType image/webp .webp' > /etc/apache2/conf-available/webp.conf \
	&& a2enconf webp

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY . /var/www/html/
RUN chmod -Rv +rx .

WORKDIR /var/www/html/cron
RUN /usr/local/bin/php /var/www/html/cron/github.php
RUN chmod -Rv +rwx /var/www/html/cron/

WORKDIR /var/www/html

CMD cron -f & apache2-foreground
