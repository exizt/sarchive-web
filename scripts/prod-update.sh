#!/bin/bash
# ----------------------------------------------------------------------
# [ prod-update.sh ]
# 
# (프로덕션 환경에서) 소스 업데이트하는 스크립트
#
# Copyright 2022 shoon
#
# git을 새로 내려받아서 업데이트하고, composer 갱신하고, 라라벨 캐시를
# 갱신한다.
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

cd "${SCRIPT_PATH}"

# git pull 명령어
git pull || exit 1

# 라라벨 폴더 및 파일 퍼미션 지정
bash ./laravel-permission.sh

# 스크립트 파일 퍼미션 조정 (퍼미션이 초기화되므로 다시 조정)
# prod-install.sh 은 권한주지 않아도 되므로 권한을 제외
cd "${SCRIPT_PATH}"
chmod 774 ./*.sh
chmod 664 ./prod-install.sh

# 컴포저 업데이트 및 라라벨 캐시 갱신
sudo docker exec -it $1 "${SCRIPT_PATH}/prod-composer-update.sh"