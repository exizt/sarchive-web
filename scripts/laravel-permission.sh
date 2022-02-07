#! /bin/bash
# 폴더 퍼미션 지정하는 스크립트

# bash handling (bash가 아니면 bash로 실행)
if [ -z "$BASH_VERSION" ]; then exec bash "$0" "$@"; exit; fi

# 스크립트의 경로
SCRIPT_PATH=$(dirname "$(readlink -f "${BASH_SOURCE[0]}")")

cd "${SCRIPT_PATH}/../web"

# 평범하게 소유권만 바꿔주자.
sudo chgrp -R www-data bootstrap/cache
sudo chgrp -R www-data storage