FROM postgres:16-alpine3.18 as database
ENV POSTGRES_DB cms
ENV POSTGRES_USER postgres
ENV POSTGRES_PASSWORD postgres
COPY schema.sql /docker-entrypoint-initdb.d

#FROM mongo:7 as mongo
#ENV MONGO_INITDB_DATABASE blog
#COPY user.seed.js /docker-entrypoint-initdb.d/

FROM php:8.3-alpine as storm
ENV APP_ENV=development
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
WORKDIR /usr/dev
#Postgres
RUN apk add postgresql-dev
RUN docker-php-ext-install pdo pdo_pgsql
RUN docker-php-ext-enable pdo pdo_pgsql
#intl
RUN apk add icu-dev
RUN apk add icu-data-full
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

FROM storm as cms
WORKDIR /usr/dev
COPY storm.php /usr/dev/storm.php
COPY src/ /usr/dev/src
COPY server/ /usr/dev/server
copy vendor/ /usr/dev/vendor


CMD php -S 0.0.0.0:80 -t /usr/dev/server