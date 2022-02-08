# 1. SArchive Project 개요
- **소스 아카이브 프로젝트**
- 매우 오래된 소스코드나 오래된 IT 정보, 오래 보관하기 위한 노트 및 문서들을 보관하기 위한 프로젝트.
- 링크
    - 개발 문서 : https://swiki.asv.kr/wiki/개발:SARChive_프로젝트
    - Github : https://github.com/exizt/sarchive-web


## 1.2. 개발 환경
* PHP 7.4 이상
* Laravel 8.0 이상


Laravel 에서 필요한 PHP 구성
* extension=openssl : 뭐였는지 기억 안 나지만 필요함
* extension=pdo_mysql : DB 연결을 위해 필요
* extension=mbstring : 뭐였는지 기억 안 남
* extension=fileinfo : 파일업로드 기능을 위해 필요한 듯


# 2. 셋팅
## 2.1. 도커 & 개발 환경
### 2.1.1. 도커 & 개발 환경 - 셋팅 과정
1. 깃 클론 및 간단 설정
    ```console
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
        ```console
        sudo docker-compose --env-file=./docker/.env.local up --build --force-recreate -d
        ```
    - DB 볼륨을 삭제할 필요가 있을 시에는 다음 명령어를 선행하여 클린 삭제해준다.
        ```console
        sudo docker-compose --env-file=.env.local down -v
        ```
5. 필요시 `APP_KEY` 갱신
    ```console
    sudo docker exec -it sarchive_webapp_1 php artisan key:generate
    ```
6. 데이터베이스 테이블 import (아래 참조)
7. 웹 접속 (http://localhost:30082)


## 2.2. 공통 도커 컨테이너 & 프로덕션
### 2.2.1. 셋팅 과정 - 공통 도커 컨테이너 & 프로덕션
1. 깃 클론 및 간단 설정
    ```console
    # 깃 클론
    git clone--depth 1 --single-branch --branch master git@github.com:exizt/sarchive-web.git sarchive-web

    # 깃 설정 (퍼미션모드 false)
    cd sarchive-web && git config core.filemode false
    
    # 스크립트 권한 부여
    chmod 774 ./scripts/*
    ```
2. 라라벨 설정
    - `web/.env.local.example`을 복사해서 `web/.env` 생성 후 값 입력.
    - 데이터베이스 연결 정보 등을 기입.
3. 캐시 설정 등
    ```console
    ./scripts/prod-init.sh 1
    ```
    - 라라벨 폴더 및 파일 퍼미션 부여 등을 하는 스크립트.

### 2.2.2. 업데이트 과정 - 공통 도커 컨테이너 & 프로덕션
```console
./scripts/prod-update.sh 1
```


# 3. 데이터베이스
## 3.1. 데이터베이스 백업
바로 접근이 가능할 경우.
```
mysqldump --routines --triggers -uroot -p SERV_SARCHIVE > sarchive_dump.20220207.sql
```


도커 이용시
```
docker exec -it sarchive_db_1 /bin/bash
cd (백업할 파일을 둘 경로)
mysqldump --routines --triggers -uroot -p SERV_SARCHIVE > sarchive_dump.20220206.sql
```


## 3.2. 데이터베이스 올리기
바로 접근이 가능할 경우.
```
mysql -uroot -p SITE_CHOSIM_LARAVEL < sarchive_dump.20220206.sql
```


도커 이용시
```
docker exec -it sarchive_db_1 /bin/bash
mysql -uroot -p sarchive < /app/scripts/sarchive_dump.20220206.sql
```


(로컬용) 한 줄로 간단히 처리하는 스크립트 이용. (경로는 마운팅 볼륨 기준)
```
sudo docker exec -it sarchive_db_1 /app/scripts/db-import-local.sh /app/scripts/sarchive_dump.20220207.sql
```


직접적으로 처리하는 방식 (경로는 호스트 기준)
```
sudo docker exec -i sarchive_db_1 mysql -uroot --password=암호 chosim < ./scripts/sarchive_dump.20220206.sql
```


# 4. 사용법
## 4.1. Artisan
```
sudo docker exec -it sarchive_webapp_1 php artisan (명령어)
```


## 4.2. Composer
```
sudo docker exec -it sarchive_webapp_1 composer update
```

# 5. 관리
## 5.1. 릴리즈 배포 및 버전 관리
1. `web/config/_app.php`에서 버전 정보 수정 후 커밋
2. `gitflow`를 이용
3. 릴리즈 명칭 : 'v~~' (예: 'v1.0.0)
4. 소스트리 > 깃 플로우 > 릴리즈 마무리
5. 'push' 실행


## 5.2. 프로덕션에서 백업
백업해야하는 항목
- `.env` 설정
- 데이터베이스 백업

참고
- 여기서는 파일 업로드 기능은 이용하지 않으므로 백업하지 않아도 됨.

