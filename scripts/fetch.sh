#!/bin/bash

# bash handling (bash가 아니면 bash로 실행)
if [ -z "$BASH_VERSION" ]; then exec bash "$0" "$@"; exit; fi

# 스크립트의 경로
SCRIPT_PATH=$(dirname "$(readlink -f "${BASH_SOURCE[0]}")") # 스크립트의 경로
PROJECT_ROOT_PATH="${SCRIPT_PATH}/.." # 위키 프로젝트 경로
LARAKIT_PATH="${PROJECT_ROOT_PATH}/aether-wiki" # LaraBaseKit 경로

# 프로젝트 경로로 이동
cd $LARAKIT_PATH

# fetch 수행
bash ./scripts/fetch.sh
