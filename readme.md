# 개요 
SArchive 프로젝트
* 오래된 코드나 문서를 정리하고 아카이빙하는 목적의 서비스
* 위키 문서 링크 : [링크](https://swiki.asv.kr/wiki/개발:SARChive_프로젝트)


개발 관련으로는 'devnote.md' 문서를 참조할 것.


# 사이트 내의 단축키

1. [C] : 뒤로가기
2. [S] : (일반적으로) 저장
3. [F] : 검색
4. [N] : 새로 글 쓰기


# 개발 환경
* Laravel 7.0 이상
* PHP 7.4 이상 (요구사항 7.2.5 이상)


Laravel 에서 필요한 PHP 구성

* extension=openssl : 뭐였는지 기억 안 나지만 필요함
* extension=pdo_mysql : DB 연결을 위해 필요
* extension=mbstring : 뭐였는지 기억 안 남
* extension=fileinfo : 파일업로드 기능을 위해 필요한 듯


# 셋팅 과정
1. git 을 내려받는다.
2. web/.env.example 을 복사해서 web/.env 를 생성한다.
    - 데이터베이스 연결 및 Secret key 등을 기입한다.
3. 도커 사용시
    1. /.local.env.example 을 복사해서 /.local.env 를 생성한다. (도커 컴포저 설정)
        - 도커를 사용 안 할 때에는 필요하지 않다.
    2. 다음의 명령어를 통해서 도커 이미지 및 컨테이너를 생성한다.
    ```console
    docker-compose --env-file=.local.env up --build --force-recreate -d
    ```
4. 도커를 사용하지 않을 때
    - web/public 까지가 document_root 가 되도록 웹서버에 설정을 해준다.
    - `composer install`을 해준다.