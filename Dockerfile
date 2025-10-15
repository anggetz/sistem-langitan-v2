FROM umaha/sistem-langitan-v2:base

COPY . .

RUN npm i && \
    composer install && \
    composer run dev
    
