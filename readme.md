# SArchive Project

오래된 소스코드나 오래된 IT 정보, 노트 및 문서들을 보관하기 위한 프로젝트입니다.

- 개발 문서 : https://swiki.asv.kr/wiki/개발:SArchive_프로젝트
- Git
    - `git@github.com:exizt/sarchive-web.git`
- URL
    - Prod: https://sarchive.asv.kr
    - Local (예시): http://localhost:30082
- 다루는 주제
    - 매우 오래된 소스코드 클립.
    - 오래된 IT 정보 등

<br><br>

## 셋팅하기
### git 저장소 받기
```shell
git clone --recurse-submodules -j8 git@github.com:exizt/sarchive-web.git sarchive-web
```

### 로컬 환경
#### 셋팅
1. 깃 클론
2. 도커 컨테이너 설정
    - `/.env.local.sample`을 복사해서 `/.env.local` 생성 후 값 입력. (디비 암호, 포트 등)
3. 도커 컨테이너 생성
    ```shell
    sudo docker-compose --env-file=.env.local --project-directory=. up --build --force-recreate -d
    ```
4. 라라벨 설정
    - `web/.env.local.example`을 복사해서 `web/.env` 생성 후 값 입력.
    - 데이터베이스 연결 정보 등을 기입.
5. 필요시 `APP_KEY` 갱신
    ```shell
    sudo docker-compose --env-file=.env.local --project-directory=. exec web php artisan key:generate
    ```
6. 데이터베이스 테이블 import (아래 참조)
7. 웹 접속


#### 도커 컨테이너 시작
도커 컨테이너 생성은 되었으나, 재부팅 등으로 컨테이너를 시작해야할 때
```shell
sudo docker-compose --env-file=.env.local --project-directory=. start
```


### 프로덕션, Staging 환경 (공통 컨테이너)
웹용 도커 컨테이너를 하나로 이용하고 있을 때에 대한 내용입니다. (개별로 컨테이너를 구성했다면 로컬 개발환경과 차이가 없음)

#### 셋팅
> 아무것도 없는 상태에서 프로젝트를 내려받고 셋팅하는 과정.

1. 깃 클론
2. 라라벨 설정
    - `web/.env.prod.example`을 복사해서 `web/.env` 생성 후 값 입력.
    - 데이터베이스 연결 정보 등을 기입.
3. 라라벨 설치 및 셋업
    - 구문: `sudo ./larabasekit/scripts/update.prod.sh (컨테이너명)`
    ```shell
    # 방법 1
    sudo docker exec -it php_laravel_web_1 bash -c "cd $(pwd) && ./larabasekit/scripts/install-laravel.sh prod"

    # 방법 2
    sudo ./larabasekit/scripts/update.prod.sh php_laravel_web_1
    ```
    - 설명: 라라벨 폴더 소유권 부여, composer 셋팅, 스토리지 심볼릭 링크 생성 등을 처리함
4. 필요시 `APP_KEY` 갱신
    ```shell
    # 예시
    sudo docker exec -it php_laravel_web_1 bash -c "cd $(pwd) && cd web && php artisan key:generate && php artisan config:cache && php artisan route:cache"
    ```
5. `storage`에 파일 업로드를 사용중일 경우, 업로드된 파일 복사.
    `web/storage/app`에 있는 파일을 FTP로 업로드
6. 데이터베이스 import (아래 참조)
7. 웹 접속


## 업데이트
### 프로덕션 환경
(1) git 내려받기
```shell
./scripts/fetch.sh
```


(2) 설정 캐시만 변경하려는 경우
```shell
sudo ./larabasekit/scripts/caching-config.prod.sh php_laravel_web_1
```


(3) 라라벨 업그레이드, 설정 변경, 캐시 변경 등의 전체적 업그레이드 과정
```shell
sudo ./larabasekit/scripts/update.prod.sh php_laravel_web_1
```
- composer 패키지 재설치
- 설정 캐시 갱신



## 데이터베이스 백업 및 복원
(로컬 환경)
* `scripts/sql` 폴더를 먼저 만들고 백업을 진행하자. `mkdir ./scripts/sql`

(로컬 환경) 데이터베이스 백업
```shell
sudo docker-compose --env-file=.env.local --project-directory=. exec db sh -c 'exec mariadb-dump --routines -uroot -p"${MARIADB_ROOT_PASSWORD}" ${MARIADB_DATABASE}' > ./scripts/sql/db_dump.local.$(date +%Y%m%d).sql
```

(로컬 환경) 데이터베이스 올리기
```shell
sudo docker-compose --env-file=.env.local --project-directory=. exec -T db sh -c 'exec mariadb -uroot -p"${MARIADB_ROOT_PASSWORD}" ${MARIADB_DATABASE}' < (백업_파일경로)
```

> 프로덕션 환경에 관련해서는 `larabasekit/readme.md`를 참조할 것.

<br>

## 사용법
### Artisan
(로컬 환경에서)
```shell
# 구문
sudo docker-compose --env-file=.env.local --project-directory=. exec web php artisan (명령어)

# 예시 (스토리지 심볼릭 링크 생성)
sudo docker-compose --env-file=.env.local --project-directory=. exec web php artisan storage:link

# 심볼릭 local.sh를 이용.
./local.sh php artisan storage:link
```

(프로덕션 환경에서)
```shell
# 구문
sudo docker exec -it (컨테이너명) bash -c "cd $(pwd) && php artisan (명령어)"

# 예시
sudo docker exec -it php_laravel_web_1 bash -c "cd $(pwd) && php artisan key:generate"
```


### Composer
(로컬 환경에서)
```shell
# docker-compose 이용
sudo docker-compose --env-file=.env.local --project-directory=. exec web composer update

# 심볼릭 local.sh 이용
./local.sh composer update
```


(프로덕션 환경에서)
```shell
# 구문
sudo docker exec -it (컨테이너명) bash -c "cd $(pwd) && composer (명령어)"

# 예시
sudo docker exec -it php_laravel_web_1 bash -c "cd $(pwd) && composer update"
```


### Docker
#### 도커 컨테이너 시작
(로컬 환경) `docker-compose`를 이용한 방식.
```shell
sudo docker-compose --env-file=.env.local --project-directory=. start
```

#### 도커 컨테이너 접속
(로컬 환경) `docker-compose`를 이용한 방식.
```shell
sudo docker-compose --env-file=.env.local --project-directory=. exec web /bin/bash
```

## 관리
### 백업 관리
배포 환경에서 백업해야 하는 항목
1. 설정 백업 : `.env`
2. 데이터베이스 백업
3. 파일 첨부 기능은 이용하지 않음.


## 프로젝트 동작에 관한 추가 정보
1. 이 프로젝트에서 필요로 하는 PHP 익스텐션
    - `extension=openssl` : 뭐였는지 기억 안 나지만 필요함
    - `extension=pdo_mysql` : DB 연결을 위해 필요
    - `extension=mbstring` : 뭐였는지 기억 안 남
2. 패키징 및 도구
    - `composer`는 사용함.
    - 이 프로젝트에서는 `npm, webpack`은 사용하지 않음.
3. 기능
    - 파일 첨부 기능 사용하지 않음.
