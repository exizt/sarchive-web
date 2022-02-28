#!/bin/bash
# ----------------------------------------------------------------------
# [ laravel-permission.sh ]
#
# 라라벨 폴더 및 파일에 소유권을 지정하는 스크립트
#
# Copyright 2022 shoon
# 
# 라라벨이 동작하려면, 캐시 등이 저장되는 경로에 쓰기 권한을 필요로 한다.
# 퍼미션을 지정하는 방법과 소유권을 주는 방법이 있는데, 여기서는 소유권을 
# apache (CentOS 계열) 또는 www-data (우분투 계열)로 주는 방법을 사용했다.
# 
# ----------------------------------------------------------------------

# bash handling (bash가 아니면 bash로 실행)
if [ -z "$BASH_VERSION" ]; then exec bash "$0" "$@"; exit; fi

# 스크립트의 경로
SCRIPT_PATH=$(dirname "$(readlink -f "${BASH_SOURCE[0]}")")

cd "${SCRIPT_PATH}/../web"

# 평범하게 소유권만 바꿔주자.
sudo chgrp -R www-data bootstrap/cache
sudo chgrp -R www-data storage

#sudo find storage -type f -exec chmod 664 {} \;
#sudo find storage -type d -exec chmod 775 {} \;

#sudo find . -type f -exec chmod 664 {} \;
#sudo find . -type d -exec chmod 775 {} \;