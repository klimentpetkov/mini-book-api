FROM php:8.3-fpm-alpine

RUN apk add --no-cache bash git curl zip unzip libzip-dev icu-dev oniguruma-dev $PHPIZE_DEPS \
  && docker-php-ext-install pdo pdo_mysql intl mbstring zip opcache \
  && pecl install redis \
  && docker-php-ext-enable redis

# opcachе (по избор)
RUN { \
  echo 'opcache.enable=1'; \
  echo 'opcache.enable_cli=1'; \
  echo 'opcache.validate_timestamps=0'; \
  echo 'opcache.memory_consumption=256'; \
} > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# копирай кода
COPY . .

# entrypoint
COPY docker/app/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

CMD ["php-fpm"]
ENTRYPOINT ["entrypoint.sh"]
