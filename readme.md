# 1. SArchive Project 개요

- 매우 오래된 소스코드나 오래된 IT 정보, 오래 보관하기 위한 노트 및 문서들을 보관하기 위한 프로젝트.
- 개발 문서 : https://swiki.asv.kr/wiki/개발:SARChive_프로젝트


## 1.2. 개발 환경
* PHP 7.4 이상
* Laravel 8.0 이상


Laravel 에서 필요한 PHP 구성
* extension=openssl : 뭐였는지 기억 안 나지만 필요함
* extension=pdo_mysql : DB 연결을 위해 필요
* extension=mbstring : 뭐였는지 기억 안 남
* extension=fileinfo : 파일업로드 기능을 위해 필요한 듯


# 2. 셋팅
## 2.1 도커 - 개발 환경 셋팅 과정
1. 깃 클론 및 간단 설정
    ```console
    # 깃 클론
    git clone git@github.com:exizt/sarchive-web.git

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


## 5.1. Composer
```
sudo docker exec -it sarchive_webapp_1 composer update
```


# To-Do
- jQuery가 사용된 부분이 많이 남아있다. 이 부분을 제거해나갈 것. 
    - 각 입력폼에서 엔터키 방지 부분이 jquery로 되어있음. 이거 바닐라 코드로 변경할 것.
    - treeJS를 이용하는 부분이 있는데, 여기는 복잡하니까 jQuery를 CDN으로 호출하는 방식으로 할 것.
    - 우측 네비게이션도 jquery 가 사용되었네.. 여기는 복잡한데...
- bootstrap 5로 변경해 나갈 것.
    - 관리자 레이아웃하고 일반 레이아웃하고 하나로 사용하는데, 이것을 분리할 것.
    - 관리자 레이아웃에서 bs 5를 먼저 적용시키고, 아카이브 레이아웃에는 차차 적용시켜 나갈 것.
- 검색 기능 강화
    - 카테고리별 검색이 되도록 할 것.
- 폰트 가독성 : 더 좋은 폰트를 찾아보자.
- 게시판 삭제 시에 기존 게시물이 어디로 이동할지...
- 북마크 목록.
- 테마 설정 가능하게
- 설정에서 폰트/폰트크기 변경 가능하게.
- 카테고리 기능을 그냥 없애도 될 거 같은데?
- 페이지 기능도 없애도 될 거 같고.


# 사이트 내의 단축키
alt + shift
- [F] : 검색
- [Z] : 뒤로가기
- [E] : (일반적으로) 편집
- [S] : (일반적으로) 저장
- [N] : 새로 글 쓰기

