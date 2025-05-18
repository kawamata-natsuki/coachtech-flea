FROM php:8.2-fpm

COPY php.ini /usr/local/etc/php/

RUN apt update \
  && apt install -y default-mysql-client zlib1g-dev libzip-dev unzip \
  && docker-php-ext-install pdo_mysql zip

RUN curl -sS https://getcomposer.org/installer | php && \
  mv composer.phar /usr/local/bin/composer && \
  composer self-update

# GDライブラリを追加
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libwebp-dev \
  && docker-php-ext-configure gd --with-jpeg --with-webp \
  && docker-php-ext-install gd

ARG USER_ID=1000
ARG GROUP_ID=1000

# ユーザーとグループを作成
RUN groupadd -f -g ${GROUP_ID} appgroup && \
  if ! id appuser &>/dev/null; then \
  useradd -m -u ${USER_ID} -g appgroup appuser; \
  fi

USER appuser

WORKDIR /var/www