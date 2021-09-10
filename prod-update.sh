#! /bin/bash

# 프로덕션 환경에서 업데이트 받는 부분을 스크립트화
# 이 스크립트를 한 단계 상위로 cp 해서 이용하도록 하자.
# 생각해보니 git pull을 할 때 문제가 생길 수도 있겠음.
cd sarchive-web
git pull

cd web
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache