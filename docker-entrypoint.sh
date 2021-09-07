#! /bin/bash

# echo "wait db server"
# dockerize -wait tcp://db:3306 -timeout 20s
# dockerize -wait tcp://data-api:5000 -timeout 20s

cd /app/web

if [ -f composer.json ]; then
    echo "laravel project exists"

    if [ -d vendor ]; then
        echo "vendor exists"

    else
        echo "install composer packages"
        # composer install -vv
    fi
else
    echo "laravel project doesn't exists..."
    # 종료하는 거를 넣어야 함...
fi

# service apache2 stop

# php artisan migrate
echo "Apache server is running..."
source /etc/apache2/envvars
apache2 -DFORGROUND
