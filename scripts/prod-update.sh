#!/bin/bash
#
# prod-update.sh
#
# ----------------------------------------------------------------------
# (프로덕션 환경에서) 소스 업데이트하는 스크립트.
#
# Copyright 2022 shoon
#
# git을 새로 내려받아서 업데이트하고, composer 갱신하고, 라라벨 캐시를
# 갱신한다.
# ----------------------------------------------------------------------

# bash handling (bash가 아니면 bash로 실행)
if [ -z "$BASH_VERSION" ]; then exec bash "$0" "$@"; exit; fi

# 파라미터가 없는 경우는 실행하지 않도록 함. (잘못된 실행 방지)
if [ "$#" -lt 1 ]; then
    echo "Parameters are required."
	exit 1
fi

# 스크립트의 경로
SCRIPT_PATH=$(dirname "$(readlink -f "${BASH_SOURCE[0]}")")

cd "${SCRIPT_PATH}"

# git pull 명령어
git pull || exit 1

# 컴포저 업데이트 및 라라벨 캐시 갱신
bash ./prod-composer-update.sh
