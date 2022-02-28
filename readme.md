# 1. SArchive Project 개요
- **소스 아카이브 프로젝트**
- 내용 : 매우 오래된 소스코드나 오래된 IT 정보, 오래 보관하기 위한 노트 및 문서들을 보관하기 위한 프로젝트.
- 링크
    - 개발 문서 : https://swiki.asv.kr/wiki/개발:SARChive_프로젝트
    - Github : https://github.com/exizt/sarchive-web
    - 로컬 : http://localhost:30082
    - Dev : http://dev-sarchive.asv.kr

## 1.2. 개발 환경
* PHP 7.4 이상
* Laravel 8.0 이상


Laravel 에서 필요한 PHP 구성
* extension=openssl : 뭐였는지 기억 안 나지만 필요함
* extension=pdo_mysql : DB 연결을 위해 필요
* extension=mbstring : 뭐였는지 기억 안 남
* extension=fileinfo : 파일업로드 기능을 위해 필요한 듯


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
        sudo docker-compose --env-file=.env.local down -v
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
    sudo ./scripts/prod-init.sh (컨테이너명)

    # 예시)
    sudo ./scripts/prod-install.sh php_laravel_web_1
    ```
    - 라라벨 폴더 소유권 부여, composer 셋팅, 스토리지 심볼릭 링크 생성 등을 처리함
4. 데이터베이스 import (아래 참조)
5. 웹 접속 (예시: http://dev-chosim.asv.kr:31080)


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
mysql -uroot -p (디비명)) < (백업 파일 경로)

# (로컬) 예시
sudo docker exec -it sarchive_db_1 /bin/bash
mysql -uroot -p sarchive < /app/scripts/sarchive_dump.20220206.sql

# (프로덕션) 예시
sudo docker exec -it mariadb-106_mariadb_1 /bin/bash
mysql -uroot -p SERV_SARCHIVE < /srv/db/shared/sarchive_dump.20220206.sql
```


# 5. 사용법
## 5.1. Artisan
```
sudo docker exec -it sarchive_webapp_1 php artisan (명령어)
```


## 5.2. Composer
```
sudo docker exec -it sarchive_webapp_1 composer update
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