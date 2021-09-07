FROM php:7.4-apache-buster

WORKDIR /app

# 필요한 것들 설치
# - wget : dockerize에서 필요
# - git, unzip : composer 에서 필요
RUN  apt-get update \
    && apt-get install -y \
    wget \
    git \
    unzip \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*

# DB 연결에 대기시킬 수 있는 기능을 하는 Dockerize 를 이용
# ENV DOCKERIZE_VERSION v0.6.1
# RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
#    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
#    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

# composer 설치
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-install bcmath pdo_mysql 
RUN a2enmod rewrite headers

#Set the ENV vars
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2

# 포트
EXPOSE 80

# 도커에서 dforground 옵션으로 동작시킬 것이기 때문에 서비스에서는 종료.
RUN service apache2 stop

# 웹 어플리케이션 지정
COPY ./vhost.conf /etc/apache2/sites-available/000-default.conf

# entrypoint.sh 복사
COPY docker-entrypoint.sh .

# composer 설정 및 실행
# COPY ./web/composer.json .
# COPY ./web/composer.lock .
ENV COMPOSER_ALLOW_SUPERUSER 1
# RUN composer install
# RUN composer install --optimize-autoloader --no-dev

ENV C_VENDOR_ROOT='/app'

# 
WORKDIR /app/web

# CMD ["apache2","-D","FOREGROUND"]
