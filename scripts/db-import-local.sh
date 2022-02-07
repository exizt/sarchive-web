#! /bin/bash
# 폴더 퍼미션 지정하는 스크립트

# bash handling (bash가 아니면 bash로 실행)
if [ -z "$BASH_VERSION" ]; then exec bash "$0" "$@"; exit; fi

# 스크립트의 경로
SCRIPT_PATH=$(dirname "$(readlink -f "${BASH_SOURCE[0]}")")

# 파라미터가 없는 경우는 실행하지 않도록 함. (파라미터는 이미지 업로드 경로)
if [ "$#" -lt 1 ]; then
    echo "Parameters are required."
	exit 1
fi

# 백업본 import 처리
mysql -uroot --password=$MYSQL_PASSWORD $DB_DATABASE < $1