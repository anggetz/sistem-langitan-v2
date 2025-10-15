FROM php:8.2-apache-bookworm

# Install Required lib
RUN apt-get update \
    && apt-get install -qqy libaio-dev libfreetype6-dev libicu-dev libjpeg62-turbo-dev libmagickwand-dev libmcrypt-dev libpng-dev libzip-dev sendmail unzip wget npm \
    && rm -fr /var/lib/apt/lists/*

# Install Composer
COPY --from=composer/composer /usr/bin/composer /usr/bin/composer

# Set workdir
WORKDIR /opt/oracle

# Add instantclient
ADD https://download.oracle.com/otn_software/linux/instantclient/1928000/instantclient-basic-linux.x64-19.28.0.0.0dbru.zip .
ADD https://download.oracle.com/otn_software/linux/instantclient/1928000/instantclient-sdk-linux.x64-19.28.0.0.0dbru.zip .

# Unzip instantclient
RUN unzip -qqo instantclient-basic-linux.x64-19.28.0.0.0dbru.zip \
    && unzip -qqo instantclient-sdk-linux.x64-19.28.0.0.0dbru.zip \
    && rm -rf instantclient-*.zip

# Set LIBRARY_PATH
ENV LD_LIBRARY_PATH=/opt/oracle/instantclient_19_28

# Install Oracle extensions
RUN echo 'instantclient,/opt/oracle/instantclient_19_28/' | pecl install oci8 \
    && docker-php-ext-enable oci8 \
    && docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient_19_28,19.28 \
    && docker-php-ext-install pdo_oci

# Install GD
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd

# Install Zip
RUN docker-php-ext-install zip

# Install Imagick
RUN pecl install imagick && docker-php-ext-enable imagick

# Install Mcrypt
RUN pecl install mcrypt-1.0.9 && docker-php-ext-enable mcrypt

# Install OPcache
RUN docker-php-ext-install opcache

# Install intl
RUN docker-php-ext-install intl

# Set PHP Production Setting
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# Hilangkan notice
RUN sed -i 's/^error_reporting = .*/error_reporting = E_ALL \& ~E_NOTICE/g' "$PHP_INI_DIR/php.ini"

# Set default workdir
WORKDIR /var/www/html

test
