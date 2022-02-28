#!/bin/bash
# ----------------------------------------------------------------------
# [ db-import-local.sh ]
# 
# MySQL/MariaDB 데이터베이스를 덤프하는 스크립트
#
# Copyright 2022 shoon
#
# 도커 컨테이너에서 쉽게 이용할 수 있게, 컨테이너 환경 변수에 있는 데이터베이스의
# 비밀번호 및 데이터베이스 이름을 활용하여 백업 복구를 실행한다.
# 
# Author: shoon
#
# Usage:
#    sudo docker exec -it (컨테이너명) (바인드된 경로)/db-import-local.sh (덤프파일 경로)
#
# Parameters:
#    첫번째: 덤프파일의 경로 (컨테이너 기준)
# ----------------------------------------------------------------------

# bash handling (bash가 아니면 bash로 실행)
if [ -z "$BASH_VERSION" ]; then exec bash "$0" "$@"; exit; fi

# 스크립트의 경로
# SCRIPT_PATH=$(dirname "$(readlink -f "${BASH_SOURCE[0]}")")

# 파라미터가 없는 경우는 실행하지 않도록 함. (파라미터는 이미지 업로드 경로)
if [ "$#" -lt 1 ]; then
    echo "Parameters are required."
	exit 1
fi

# 백업본 import 처리
# mysql -uroot --password=${MYSQL_ROOT_PASSWORD} ${MYSQL_DATABASE} < $1
mysqldump --routines --triggers -uroot --password=${MYSQL_ROOT_PASSWORD} ${MYSQL_DATABASE} > $1