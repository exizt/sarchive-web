# 1. SArchive Project 개요
- **소스 아카이브 프로젝트**
- 내용 : 매우 오래된 소스코드나 오래된 IT 정보, 오래 보관하기 위한 노트 및 문서들을 보관하기 위한 프로젝트.
- 링크
    - 개발 문서 : https://swiki.asv.kr/wiki/개발:SARChive_프로젝트
    - Github : https://github.com/exizt/sarchive-web
    - 로컬 : http://localhost:30082
    - Dev : http://dev-sarchive.asv.kr
    - Prod : https://sarchive.asv.kr

## 1.2. 동작 환경
* PHP 8.0 이상
* Laravel 8.0 이상


Laravel 에서 필요한 PHP 구성
* extension=openssl : 뭐였는지 기억 안 나지만 필요함
* extension=pdo_mysql : DB 연결을 위해 필요
* extension=mbstring : 뭐였는지 기억 안 남


참고
* 이 프로젝트에서는 npm, webpack은 사용하지 않음.
* composer는 사용함.
* 파일 첨부 기능 사용하지 않음.


# 2. 로컬 개발 환경
## 2.1. 최초 셋팅 과정
1. 깃 클론 및 간단 설정
    ```shell
    # 깃 클론
    git clone git@github.com:exizt/sarchive-web.git sarchive-web

    # 깃 설정 (퍼미션모드 false)
    cd sarchive-web && git config core.filemode false
    
    # 스크립트 권한 부여
    chmod 774 ./scripts/*

    # 라라벨 폴더 및 파일 퍼미션 부여
    ./scripts/laravel-permission.sh
    ```
2. 도커 환경 변수 설정하기
    - `docker/.env.local.example`을 복사해서 `docker/.env.local` 생성 후 값 입력. (디비 암호, 포트 등)
3. 라라벨 설정
    - `web/.env.local.example`을 복사해서 `web/.env` 생성 후 값 입력.
    - 데이터베이스 연결 정보 등을 기입.
4. 도커 컨테이너 생성
    - 다음의 명령어 실행
        ```shell
        sudo docker-compose --env-file=./docker/.env.local up --build --force-recreate -d
        ```
    - DB 볼륨을 삭제할 필요가 있을 시에는 다음 명령어를 선행하여 클린 삭제해준다.
        ```shell
        sudo docker-compose --env-file=./docker/.env.local down -v
        ```
5. 필요시 `APP_KEY` 갱신
    ```shell
    sudo docker exec -it sarchive_webapp_1 php artisan key:generate
    ```
6. 데이터베이스 테이블 import (아래 참조)
7. 웹 접속 (http://localhost:30082)


## 2.2. 도커 컨테이너 시작
도커 컨테이너 생성은 되었으나, 재부팅 등으로 컨테이너를 시작해야할 때
```shell
sudo docker start sarchive_db_1 sarchive_webapp_1
```


# 3. 프로덕션 환경 (공통 컨테이너)
웹용 도커 컨테이너를 하나로 이용하고 있을 때에 대한 내용입니다. (개별로 컨테이너를 구성했다면 로컬 개발환경과 차이가 없음.)

## 3.1. 최초 셋팅 과정
1. 깃 클론 및 간단 설정
    ```shell
    # 깃 클론
    git clone --depth 1 --single-branch --branch master git@github.com:exizt/sarchive-web.git sarchive-web

    # 깃 설정 (퍼미션모드 false)
    cd sarchive-web && git config core.filemode false

    # 스크립트 권한 부여
    chmod 774 ./scripts/*
    ```
2. 라라벨 설정
    - `web/.env.prod.example`을 복사해서 `web/.env` 생성 후 값 입력.
    - 데이터베이스 연결 정보 등을 기입.
3. 캐시 설정 등
    ```shell
    # 구문
    sudo ./scripts/prod-install.sh (컨테이너명)

    # 예시)
    sudo ./scripts/prod-install.sh php_laravel_web_1
    ```
    - 라라벨 폴더 소유권 부여, composer 셋팅, 스토리지 심볼릭 링크 생성 등을 처리함
4. 데이터베이스 import (아래 참조)
5. 웹 접속 (예시: http://dev-sarchive.asv.kr)


## 3.2. 업데이트 과정
git 업데이트
```shell
# 구문
./scripts/prod-update.sh (컨테이너명)

# 예시)
su - shoon
cd /srv/www/php/sarchive.serv/sarchive-web
./scripts/prod-update.sh php_laravel_web_1
```

스크립트에 기입되어있는 필요한 진행 과정은 다음과 같음.
1. `git pull` : git 내려받기
2. 라라벨 폴더 및 파일들(bootstrap/cache, storage) 권한 부여
3. 스크립트 파일들(scripts/*.sh) 권한 부여
4. `composer install --optimize-autoloader --no-dev` : composer.lock을 토대로 설치.
5. `php artisan config:cache` : 'config 설정' 캐시 갱신
6. `php artisan route:cache` : 'route 설정' 캐시 갱신


# 4. 데이터베이스
## 4.1. 데이터베이스 백업
바로 접근이 가능할 경우.
```shell
# 구문
mysqldump --routines --triggers -uroot -p (디비명) > (백업될 파일명)

# 예시
mysqldump --routines --triggers -uroot -p SERV_SARCHIVE > sarchive_dump.20220207.sql
```


(로컬) 방법1. 스크립트 이용 (경로는 마운팅 볼륨 기준)
```shell
# 구문
sudo docker exec -it (컨테이너명) /app/scripts/db-export-local.sh (백업될 파일명)

# 예시
sudo docker exec -it sarchive_db_1 /app/scripts/db-export-local.sh /app/scripts/sarchive_dump.local.20220228.sql
```


(로컬, 프로덕션) 방법2. 도커 컨테이너 이용 (경로는 호스트 기준)
```shell
# 구문
sudo docker exec -i (컨테이너명) mysqldump --routines --triggers -uroot -p (디비명) > (백업될 파일 경로-호스트 기준)

# (로컬) 예시
sudo docker exec -i sarchive_db_1 mysqldump --routines --triggers -uroot -p sarchive > ./scripts/sarchive_dump.local.20220210.sql

# (프로덕션) 예시
sudo docker exec -i mariadb-106_mariadb_1 mysqldump --routines --triggers -uroot -p SERV_SARCHIVE > ./scripts/sarchive_dump.20220206.sql
```


(로컬, 프로덕션) 방법3. 도커 컨테이너 이용. 일반적인 방법
```shell
# 구문
sudo docker exec -it (컨테이너명) /bin/bash
mysqldump --routines --triggers -uroot -p (디비명) > (백업될 파일경로)

# (로컬) 예시
sudo docker exec -it sarchive_db_1 /bin/bash
mysqldump --routines --triggers -uroot -p sarchive > /app/scripts/sarchive_dump.20220206.sql

# (프로덕션) 예시
sudo docker exec -it mariadb-106_mariadb_1 /bin/bash
mysqldump --routines --triggers -uroot -p SERV_SARCHIVE > /srv/db/shared/sarchive_dump.20220206.sql
```


## 4.2. 데이터베이스 올리기
바로 접근이 가능할 경우.
```shell
# 구문
mysql -uroot -p (디비명) < (백업 파일명)

# 예시
mysql -uroot -p SERV_SARCHIVE < sarchive_dump.20220206.sql
```


(로컬) 방법1. 스크립트 이용 (경로는 마운팅 볼륨 기준)
```shell
# 구문
sudo docker exec -it (컨테이너명) /app/scripts/db-import-local.sh (백업 파일명)

# 예시
sudo docker exec -it sarchive_db_1 /app/scripts/db-import-local.sh /app/scripts/sarchive_dump.local.20220206.sql
```


(로컬, 프로덕션) 방법2. 도커 컨테이너 이용 (경로는 호스트 기준)
```shell
# 구문
sudo docker exec -i (컨테이너명) mysql -uroot -p (디비명) < (백업 파일명)

# (로컬) 예시
sudo docker exec -i sarchive_db_1 mysql -uroot -p sarchive < ./scripts/sarchive_dump.local.20220206.sql

# (프로덕션) 예시
sudo docker exec -i mariadb-106_mariadb_1 mysql -uroot -p SERV_SARCHIVE < ./scripts/sarchive_dump.20220206.sql
```


(로컬, 프로덕션) 방법3. 도커 컨테이너 이용. 일반적인 방법
```shell
# 구문
sudo docker exec -it (컨테이너명) /bin/bash
mysql -uroot -p (디비명) < (백업 파일 경로)

# (로컬) 예시
sudo docker exec -it sarchive_db_1 /bin/bash
mysql -uroot -p sarchive < /app/scripts/sarchive_dump.20220206.sql

# (프로덕션) 예시
sudo docker exec -it mariadb-106_mariadb_1 /bin/bash
mysql -uroot -p SERV_SARCHIVE < /srv/db/shared/sarchive_dump.20220206.sql
```


# 5. 사용법
## 5.1. Artisan
(로컬 환경에서)
```shell
# 구문
sudo docker exec -it sarchive_webapp_1 php artisan (명령어)

# 예시 (스토리지 심볼릭 링크 생성)
sudo docker exec -it sarchive_webapp_1 php artisan storage:link
```


(프로덕션 환경에서)
```shell
# 구문
sudo docker exec -it (컨테이너명) bash -c "cd $(pwd) && artisan (명령어)"

# 예시
sudo docker exec -it php_laravel_web_1 bash -c "cd $(pwd) && artisan (명령어)"
```


## 5.2. Composer
(로컬 환경에서)
```shell
sudo docker exec -it sarchive_webapp_1 composer update
```


(프로덕션 환경에서)
```shell
# 구문
sudo docker exec -it (컨테이너명) bash -c "cd $(pwd) && composer (명령어)"

# 예시
sudo docker exec -it php_laravel_web_1 bash -c "cd $(pwd) && composer update"
```


## 5.3. Docker
### 5.3.1. 도커 컨테이너 시작
```shell
sudo docker start (컨테이너명1) (컨테이너명2)
```

### 5.3.2. 도커 컨테이너 접속
```shell
sudo docker exec -it sarchive_webapp_1 /bin/bash
sudo docker exec -it sarchive_db_1 /bin/bash
```


# 6. 관리
## 6.1. 릴리즈 배포 및 버전 관리
배포 및 버전 관리 과정
1. `web/config/_app.php`에서 버전 정보 수정 후 커밋
2. (`gitflow`기능을 이용해서 브랜치 관리)
3. 소스트리의 경우
    1. 소스트리 > 깃 플로우 > 릴리즈 > 릴리즈 명칭 : 예 `v22.99`
    2. 소스트리 > 깃 플로우 > 릴리즈 마무리
    3. Push
4. SmartGit의 경우
    1. Git-Flow > Start Release > Release Name : `v22.99` 입력 후 'Start' 클릭
    2. Git-Flow > Finish Release.. > 


## 6.2. 백업 관리
배포 환경에서 백업해야 하는 항목
1. 설정 백업 : `.env`
2. 데이터베이스 백업
3. 파일 첨부 기능은 이용하지 않음.


# 7. 문제 해결
## `SQLSTATE[HY000] [1045] Access denied`
`SQLSTATE[HY000] [1045] Access denied for user 'forge'@'127.0.0.1' (using password: NO)`
* 원인
    - 데이터베이스 설정이 안 되어있거나 인식이 안 되는 상황
* 해결
    - `.env`에 데이터베이스 설정을 확인해보고, 프로덕션에서는 추가로 `php artisan config:cache`를 해준다. 


## `net::ERR_HTTP_RESPONSE_CODE_FAILURE (500 오류)`
net::ERR_HTTP_RESPONSE_CODE_FAILURE (500 오류)
* views 캐싱이 안 되서 발생할 수 있다.
* `chown -R apache:apache storage`


# 8. 기획, 구성
## 8.1. 사용되는 URL 목록
* / : 아카이브 선택 화면
* /archives/{아카이브id} : 아카이브의 문서 조회
* 문서
    * /doc/{문서id}
    * /doc/create
    * /doc/{문서id}/edit
* 폴더, 카테고리, 탐색기
    * 폴더
        * /folders/{폴더id}
        * /folders/{폴더id}?only=1
    * 탐색기
        * /explorer/{아카이브id}/{폴더경로} : 폴더 탐색기
    * 검색기
        * /archives/{아카이브id}/search : 검색기
    * 카테고리
        * /archives/1/category/{카테고리명}

## 8.2. 코드 구성
컨트롤러
* Admin/ : 관리자 모드 관련
* Archive/ : 아카이브 관련
* Auth/ : 라라벨에서 추가된 인증 관련

view
* `admin/` : 관리자 모드 관련
* `app/`
* _`auth/`_ : 라라벨에서 추가된 인증 관련
* _`components/`_ : 라라벨에서 추가된 컴포넌트들
* `layouts/` : 레이아웃 구성