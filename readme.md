# SArchive 프로젝트

 매우 오래된 소스코드나 오래된 IT 정보, 오래 보관하기 위한 노트 및 문서들을 보관하기 위한 서비스.


개요
* 위키 문서 링크 : https://swiki.asv.kr/wiki/개발:SARChive_프로젝트
* 내용 예시)
  * 윈도우 7 사용법 등과 같은 오래된 내용.
  * 소설, 시, 글쓰기, 일기와 같은 일반 문서.


소스 코드 및 개발에 관련된 전문 내용은 같은 폴더의 'devnote.md' 문서를 참조할 것.


# 사이트 내의 단축키
alt + shift
- [F] : 검색
- [Z] : 뒤로가기
- [E] : (일반적으로) 편집
- [S] : (일반적으로) 저장
- [N] : 새로 글 쓰기


# 개발 환경
* PHP 7.3 이상
* Laravel 8.0 이상


Laravel 에서 필요한 PHP 구성

* extension=openssl : 뭐였는지 기억 안 나지만 필요함
* extension=pdo_mysql : DB 연결을 위해 필요
* extension=mbstring : 뭐였는지 기억 안 남
* extension=fileinfo : 파일업로드 기능을 위해 필요한 듯


# 도커 개발 환경 셋팅 과정
1. git 을 내려받는다.
2. web/.env.example 을 복사해서 web/.env 를 생성한다.
    - 데이터베이스 연결 및 Secret key 등을 기입한다.
3. 도커 사용시
    1. /.local.env.example 을 복사해서 /.local.env 를 생성한다. (도커 컴포저 설정)
        - 도커를 사용 안 할 때에는 필요하지 않다.
    2. 다음의 명령어를 통해서 도커 이미지 및 컨테이너를 생성한다.
    ```console
    docker-compose --env-file=.env.local.env up --build --force-recreate -d
    ```
    3. DB 볼륨을 삭제할 필요가 있을 때는 다음 명령어를 통해서 볼륨까지 클린 삭제를 한 후에 재생성을 한다.
    ```console
    docker-compose --env-file=.env.local.env down -v
    ```
4. 도커를 사용하지 않을 때
    - web/public 까지가 document_root 가 되도록 웹서버에 설정을 해준다.
    - `composer install`을 해준다.


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