FROM ghcr.io/umaha-ac-id/sistem-langitan-v2:base

COPY . .

RUN npm i && \
    composer install && \
    chmod -R 777 /var/www/html/storage
