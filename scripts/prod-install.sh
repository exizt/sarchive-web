#!/bin/bash
# ----------------------------------------------------------------------
# [ prod-install.sh ]
#
# (프로덕션 환경에서) 최초 1회 실행하는 스크립트
#
# Copyright 2022 shoon
#
# 라라벨 폴더 및 파일 퍼미션 지정하고, composer 생성하고, 라라벨 캐시를
# 생성한다.
#
# 파라미터:
#   - 첫번째 파라미터 : 도커 컨테이너 ID. PHP가 실행 중인 도커 컨테이너 ID를 
#         넘겨받는다. composer 등을 이용하는데에 필요하다.
# ----------------------------------------------------------------------

# bash handling (bash가 아니면 bash로 실행)
if [ -z "$BASH_VERSION" ]; then exec bash "$0" "$@"; exit; fi

# 파라미터가 없는 경우는 실행하지 않도록 함. (잘못된 실행 방지)
if [ "$#" -lt 1 ]; then
    echo "Parameters are required."
	exit 1
fi

# 스크립트의 경로 (절대 경로를 가져옴)
SCRIPT_PATH=$(dirname "$(readlink -f "${BASH_SOURCE[0]}")")
# 스크립트 파일명
SCRIPT_NAME=${0##*/}

# 현재 경로로 cd
cd "${SCRIPT_PATH}"

# 라라벨 폴더 및 파일 퍼미션 지정
bash ./laravel-permission.sh

# 컴포저 업데이트 및 라라벨 캐시 갱신
sudo docker exec -it $1 "${SCRIPT_PATH}/prod-composer-update.sh"
# bash ./prod-composer-update.sh

# 스토리지 심볼릭 링크 생성
# sudo docker exec -it $1 bash -c "cd ${SCRIPT_PATH}/../web && php artisan storage:link"