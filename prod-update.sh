#! /bin/bash

# 프로덕션 환경에서 업데이트 받는 부분을 스크립트화
# 이 스크립트를 한 단계 상위로 cp 해서 이용하도록 하자.
# 생각해보니 git pull을 할 때 문제가 생길 수도 있겠음.
{
  # 파라미터가 없으면 종료
  if [ "$#" -lt 1 ]; then
    echo "$# is Illegal number of parameters."
    exit 1
  fi

  # git pull 명령어
  git_pull_command="git pull"

  # composer 명령어
  composer_install_command="composer install --optimize-autoloader --no-dev && php artisan config:cache && php artisan route:cache"

  # 명령어 통합 및 실행
  all_command="${git_pull_command} && cd web && ${composer_install_command}"
  eval $all_command
  exit
}