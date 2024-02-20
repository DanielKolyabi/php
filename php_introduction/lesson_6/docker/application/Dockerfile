FROM php:8.0-fpm-alpine

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

#RUN docker-php-ext-install bcmath \
#    && docker-php-ext-install calendar \
#    && docker-php-ext-install ftp

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer -q

COPY php.ini /usr/local/etc/php/conf.d/php-custom.ini
COPY run.sh /root

WORKDIR /app/mysite.local
VOLUME /app/mysite.local

CMD ["sh", "/root/run.sh"]