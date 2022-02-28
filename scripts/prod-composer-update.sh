#!/bin/bash
# ----------------------------------------------------------------------
# [ prod-composer-update.sh ]
# 
# (프로덕션 환경에서) laravel composer 및 캐시 처리
#
# Copyright 2022 shoon
#
# ----------------------------------------------------------------------

# bash handling (bash가 아니면 bash로 실행)
if [ -z "$BASH_VERSION" ]; then exec bash "$0" "$@"; exit; fi

# 스크립트의 경로
SCRIPT_PATH=$(dirname "$(readlink -f "${BASH_SOURCE[0]}")")

cd "${SCRIPT_PATH}/../web"

# composer 명령어
composer install --optimize-autoloader --no-dev

# config 설정 캐시 갱신
php artisan config:cache

# route 설정 캐시 갱신
php artisan route:cache
