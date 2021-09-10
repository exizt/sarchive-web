#! /bin/bash

# 프로덕션 환경에서 업데이트 받는 부분을 스크립트화
git pull
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache