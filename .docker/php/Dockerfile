FROM php:8.2-fpm

# PDO MySQLなど必要な拡張をインストール
RUN docker-php-ext-install pdo pdo_mysql

# 作業ディレクトリの設定
WORKDIR /var/www/html