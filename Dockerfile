FROM umaha/sistem-langitan-pendaftaran:base

COPY . .

RUN npm i && \
    composer install && \
    composer run dev
    
