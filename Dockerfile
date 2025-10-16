FROM umaha/sistem-langitan-v2:base

COPY . .

RUN npm i && \
    composer install --no-dev --optimize-autoloader && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    chown -R 775 /var/www/html/storage
