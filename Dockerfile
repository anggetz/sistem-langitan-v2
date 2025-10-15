FROM umaha/sistem-langitan-v2:base

COPY . .

RUN composer install
    # npm i && \
    # composer run dev
    
