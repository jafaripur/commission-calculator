FROM php:8.0.16-cli-alpine

LABEL description="Dockerfile for build in https://github.com/jafaripur/commission-calculator"
LABEL org.opencontainers.image.authors="mjafaripur@yahoo.com"

ARG APP_SECRET
ARG CURRENCY_FREAK_API
ARG BIN_TABLE_API
ARG BIN_CODES_API

RUN apk update && apk upgrade && echo "UTC" > /etc/timezone \
	&& apk add --no-cache autoconf gcc binutils binutils-dev g++ libtool make \
	libmcrypt-dev zlib-dev libzip-dev ${PHPIZE_DEPS} \
	&& docker-php-source extract \
	&& docker-php-ext-install pcntl opcache zip \
	&& docker-php-source delete \
	&& apk del bash autoconf gcc binutils binutils-dev g++ libtool make ${PHPIZE_DEPS} \
	&& mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
	&& rm -rf /var/tmp/* \
	&& rm -rf /tmp/* \
	&& rm -rf /var/cache/apk/* \
	&& rm -rf /var/lib/apt/lists/* \
	&& rm -rf /var/cache/*

COPY docker/config/php.prod.ini /usr/local/etc/php/conf.d/99-custom.ini

RUN  adduser \
  --disabled-password \
  --home /app \
  --gecos '' app_user \
  && chown -R app_user /app

WORKDIR /app

COPY --chown=app_user . .

USER app_user

COPY --chown=app_user --from=composer:latest /usr/bin/composer .

ENV COMPOSER_HOME /app/.composer

RUN /app/docker/test-env.sh $APP_SECRET $CURRENCY_FREAK_API $BIN_TABLE_API $BIN_CODES_API

RUN rm -rf /app/docker && mkdir /app/.composer/ \
	&& /app/composer validate --strict \
	&& /app/composer install -o \
	&& /app/composer du -a \
	&& /app/composer dump-env test \
	&& /app/composer run-script psalm \
	&& /app/composer run-script phplint \
	&& /app/composer clear-cache \
	&& rm /app/composer \
	&& rm -rf /app/.composer \
	&& rm -rf /tmp/*

ENTRYPOINT ["php", "/app/vendor/bin/phpunit"]