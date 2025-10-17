FROM umaha/sistem-langitan-v2:base

COPY . .

RUN npm i && \
    composer install && \
    chown -R 775 /var/www/html/storage
