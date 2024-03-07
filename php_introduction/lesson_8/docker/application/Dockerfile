FROM php:8.0-fpm-alpine

COPY php.ini /usr/local/etc/php/conf.d/php-custom.ini
COPY run.sh /root

RUN apk add \
    htop \
    nano \
    autoconf \
    curl \
    wget \
    git \
    zip \
    unzip \
    oniguruma \
    openssl

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer -q

RUN apk add \
    --no-cache $PHPIZE_DEPS \
    --update linux-headers \
    && pecl install xdebug-3.3.1 \
    && docker-php-ext-enable xdebug

WORKDIR /app/mysite.local
VOLUME /app/mysite.local

CMD ["sh", "/root/run.sh"]