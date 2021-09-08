#! /bin/bash

cd /app

# 필요한 패키지 및 초반 셋팅
if [ -f composer.json ]; then
    if [ -d vendor ]; then
        echo "vendor exists"
        # vendor 가 있으므로 composer install 을 하지 않고 진행
    else
        if [ -d "/app/web/bootstrap/cache" ]; then
            # 여기에 해당하는 게 있으면 삭제를 먼저 하지 않으면 
            # php artisan package:discover --ansi handling the post-autoload-dump event returned with error code 1
            # 오류를 만날 수 있다.
            rm -f /app/web/bootstrap/cache/packages.php
            rm -f /app/web/bootstrap/cache/services.php
        fi
        composer install

        # 처음 구동일 것이라고 가정하고. 여기서 서버 네임 지정하는 부분 추가.
        echo "ServerName localhost" >> /etc/apache2/apache2.conf
    fi
else
    echo "composer.json not exists"
fi


# php artisan migrate


# 서버 실행
echo "Apache server is running..."
source /etc/apache2/envvars
exec apache2 -DFOREGROUND