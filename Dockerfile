FROM umaha/sistem-langitan-v2:base

COPY . .

RUN chown -R 775 /var/www/html/storage && \
    npm i && \
    composer install 
