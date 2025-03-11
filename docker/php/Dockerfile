# PHP 8.2 以上を指定（Laravel 11 に対応）
FROM php:8.2-fpm

# 必要なパッケージをインストール
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql gd xml

# Xdebugのインストール
RUN pecl install xdebug

# Xdebugの設定をコンテナ内にコピー
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www

# 権限を調整
RUN chown -R www-data:www-data /var/www
