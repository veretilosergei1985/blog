FROM composer:1.9.3 as composer
COPY composer.* /app/
WORKDIR /app
RUN set -xe \
 && composer install

FROM php:7.4.12-fpm
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# install node 12 to serve vue app in watch mode when developing
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash -
RUN apt-get install -y nodejs
RUN apt-get install -y git
# add needed php extensions
RUN apt-get install -y libpq-dev cron \
        && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
        && docker-php-ext-install pdo pdo_pgsql pgsql \
        && pecl install xdebug-2.8.1 \
        && docker-php-ext-enable xdebug
RUN apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev
WORKDIR /srv
RUN chown -R www-data /srv
COPY . ./
COPY --from=composer /app/vendor ./vendor
