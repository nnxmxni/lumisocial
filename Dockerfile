FROM php:8.2 as PHP

WORKDIR /var/www/lumisocial

COPY . .

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    make \
    autoconf \
    zlib1g-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    libevent-dev \
    libicu-dev \
    libidn11-dev \
    libidn2-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

ENV PORT=9000

RUN chmod +x ./docker/entrypoint.sh

ENTRYPOINT [ "./docker/entrypoint.sh" ]
