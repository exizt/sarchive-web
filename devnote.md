# 원격 서버에서의 유의점
* 파일 첨부 기능을 구현하지 않았기 때문에, 원격 서버에 올라간 것과 git 의 코드는 동일하다.
* `.env`만 수정을 해주면 된다. 
* 다른 점은 데이터베이스 뿐이므로. `.env`와 `데이터베이스`의 백업만 하면 된다.


# 빌드&배포
## 다음 릴리즈 배포시
1. 소스트리 gitflow 를 이용
2. 릴리즈 : 'v~~' (예: 'v1.0.0)
3. config/_app.php 에서 버전 정보 수정 후 커밋
4. 소스트리 > 깃 플로우 > 릴리즈 마무리
5. 'push' 실행


라이브러리 업데이트
1. 원격 서버가 php 7.3이므로, 로컬 환경과 다르기 때문에, 동일하게 맞춘 docker container 내부에서 다음의 명령어를 수행
2. `composer update`
3. composer.lock 파일이 갱신이 된다. 


## 원격 서버에 설치
원격에 설치
1. `git clone --depth 1 --single-branch --branch master git@github.com:exizt/sarchive-web.git sarchive-web`
2. `cd sarchive-web/web`
3. `composer install --optimize-autoloader --no-dev`
4. web/.env 설정
5. `php artisan config:cache` : 'config 설정' 캐시 갱신
6. `php artisan route:cache` : 'route 설정' 캐시 갱신
7. `php artisan migrate` : 테이블 마이그레이션
8. `chown -R apache:apache storage` : 특정 폴더의 퍼미션 조정 (blade가 캐시를 사용해야하기 때문)


원격에서 업데이트 받기
1. 원격) `./prod-update.sh 1` 스크립트 실행 (아래의 내용을 하나로 모은 스크립트)
  1. `git pull` : git 내려받기
  2. `cd web`
  3. `composer install --optimize-autoloader --no-dev` : composer.lock을 토대로 설치.
  4. `php artisan config:cache` : 'config 설정' 캐시 갱신
  5. `php artisan route:cache` : 'route 설정' 캐시 갱신
2. 필요시 테이블 마이그레이션 : `php artisan migrate`

npm 은 아직 활용하지 않는 중. 조만간 활용하게 될 듯 한데. 아직은 보류. 


## 배포시 발생 가능한 케이스
`SQLSTATE[HY000] [1045] Access denied for user 'forge'@'127.0.0.1' (using password: NO)`
* env 설정이 안 되어있거나, config:cache 를 재생성 해야한다. 
* `web/.env`에 설정을 해주고,
* `php artsian config:cache`를 해준다.


net::ERR_HTTP_RESPONSE_CODE_FAILURE (500 오류)
* views 캐싱이 안 되서 발생할 수 있다.
* `chown -R apache:apache storage`


# Composer
## 개발 IDE에서 vendor 이용하기
개발 환경에서 web/vendor 를 생성하고 싶을 때에는 다음의 명령어를 실행한다.
```
composer install --no-scripts
```

composer.lock을 업데이트하고 싶다면, 도커 컨테이너에서 composer update 를 수행하도록 한다.
이유는 scripts 가 실행되면서 bootstrap/cache 를 손대게 되는데, 복잡하게 꼬일여지가 있기 때문이다. 거기다 composer.lock 도 바뀌게 되니까 이래저래 복잡.


원칙을 정리해보자면,
* composer update 는 '도커 컨테이너'에서 한다. (composer.lock 이 갱신되고, bootstrap/cache에 영향을 준다)
* web/vendor가 필요하다면 호스트에서 'composer install --no-scripts'를 한다.

옵션 설명
* composer.lock을 기준으로 vendor를 셋팅한다.
* scripts를 통해서 bootstrap/cache/packages.php, bootstrap/cache/services.php, .env 등이 생성되거나 변경될 수 있으므로 'no scripts'옵션을 주어서 실행하도록 한다.


# 개발 메모
컨트롤러 추가 시. 

* `php artisan make:controller PhotoController --resource`
* `php artisan make:controller Admin/PhotoController --resource`



모델 추가 시

* `php artisan make:model --migration Post`
* `php artisan make:model --migration Models/Post`



# 사용되는 URL 목록
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


# Todo
* 게시판 삭제 시에 기존 게시물이 어디로 이동할지... 
* 북마크 목록.
* 검색 기능 개선. 
  * 게시판 별로 검색. 카테고리 별로 검색
* 테마 설정 가능하게
* 설정에서 폰트/폰트크기 변경 가능하게.
* 카테고리 기능을 그냥 없애도 될 거 같은데?
* 페이지 기능도 없애도 될 거 같고.


# 개념 및 단위
## 기본 개념 단위

아카이브 Archive
* 공간의 구분 단위.
* '개발 문서', '사적인 문서' 와 같이 매우 큰 구분으로 공간을 나누는 구분이다. 
* 검색 시에 해당 아카이브 내에 한정해서 검색한다.
* 상세 설명
  * 블로그를 예로들면 '블로그 하나'를 지칭한다고 생각하면 된다. 사이트나 카페로 치면 '하나의 사이트/카페'라고 볼 수 있다.
  
문서 document
* 문서 게시물의 단위. 

폴더 folders
* 문서들을 묶어두는 단계성 구분 묶음 단위.
* 상세 설명
  * 일종의 '카테고리' 개념. 블로그나 카페 같은 곳의 카테고리 같은 개념
  * 윈도우의 폴더와 비슷한 방식으로 사용할 수 있게 확장 구현함.
  * 트리형태로 구현된다. 폴더명은 변경이 가능하나 내부적으로는 id값을 갖고 있고 계층형 구조를 갖고 있다.

분류 category
* 중복이 가능한 분류 단위.
* 상세 설명 
  * 미디어위키의 다중, 중복, 재귀 가능한 방식의 분류 기능을 참조. 
  * 중복적 분류가 가능한 태그 같은 개념.

페이지 page
* 웹 페이지. 개별적으로 작성되는 웹페이지.


마킹
* watchlist(bookmark) : 주시하기. 최근에 신경쓰는 문서.
* favorite : 자주 찾는 문서.
* 인덱싱을 고려할 때에 별도의 테이블로 구성하는 것이 좋을 듯.



# 기능
## 분류 기능
분류 접속
* 분류 명칭 으로 접근 가능하게 함.
* 분류 명칭에 대해서는 only one 으로 구성.
* 분류 명칭에 따라서는 redirect 넘겨주기 가 되도록 함.


다중 상위 분류
* 상위 분류는 다중으로 구성가능하게 함.

## 경로 기능
과거의 '카테고리' 또는 '메뉴' 와 같은 목적.

트리 형식으로 구현됨.

문서의 상단에 '개발 > 프론트 엔드 > 자바스크립트' 같은 형태로 표시함.


## 검색 기능

0. 기본 검색 : 전체 문서 중에서 최근 5년 이내 문서에서 검색함. Markdown 문서의 경우에는 markdown 내용 중에서 검색.
1. 상세 검색 : 전체 문서 중에서 검색. 전체 탐색이므로 속도가 느릴 수 있음.
2. 특정 분류 문서 중에서 검색. 

## 목록 기능

'네모 형식'으로 보기.
'제목 목록' 으로 보기.
'제목&내용 일부' 으로 보기.


## 글 작성 기능

항목
1. 게시판 선택 : 해당되는 게시판을 선택. select box 를 이용. 향후에는 확장 검색이 가능하도록 함.
2. 분류 : [] 형태로 분류를 복수 입력할 수 있게 함. 텍스트로 입력함. (일일이 선택하는 게 더 번거롭기 때문...)
3. 글 출처 
  1. 내가 작성함
  2. 펌 글 => 출처 링크나 텍스트 입력 가능하게 함.


# 코드, 백 엔드

## 데이터베이스
Archives | 아카이브 목록 테이블
* 프로필 테이블
* 테이블명 : sa_archives (이전 sa_profiles)
* 설명 : 유저에 대응되어서 생성된 아카이브의 구분값. 아카이브를 여러개 생성할 수 있게 해주는 것.
* 컬럼 
  * id
  * user_id
  * name
  * text : 설명 텍스트 등.
  * index : 정렬 순서  
  * (deprecated) root_board_id
* 인덱스
  * idx_sa_archives_user_index : (user_id, id) user_id 를 기준으로 profiles 를 조회하기 위함.


Documents | 문서 테이블
* 테이블명 : sa_documents (이전 sa_archives)
* 설명 : 글 내용. 
* 참고) 차후에 다중 구성을 할 수 있음. (탭 구성으로 해서 엑셀 문서 처럼...)
* 컬럼
  * id : 문서 ID
  * title : 문서 제목
  * content : 문서 본문
  * reference : 출처. 링크 등.
  * summary_var : 내용 요약글. (varchar 255)
  * 분류 관련
    * archive_id : 아카이브 id  
    * folder_id : 폴더 id
    * category : `[분류명][분류명2]` 형태로 입력되는 컬럼.
* 인덱스
  * idx_sa_documents_latest : (archive_id, created_at desc) 인덱스. 아카이브에서 목록 불러올 때 이용.
  * idx_sa_documents_folder_latest : 폴더에서 목록 불러올 때 이용.
  * fulltext_index (title, content) 'Full Text' : 검색용 인덱스


Folders | 폴더 테이블
* 테이블명 : sa_folders (이전 sa_boards)
* 컬럼
  * id : (PK) 폴더 시스템 id
  * archive_id : 아카이브 id
  * name : 폴더 이름
  * comments : 부가 설명
  * parent_id : 상위 폴더 시스템 id
  * doc_count : 해당 폴더의 문서 수
  * index : 정렬 순서
  * depth : 깊이
  * system_path (string) : 경로 `2/33/44/` 형태로 입력되는 컬럼
* 인덱스
  * pk 인덱스 : (id)
  * idx_sa_folders_index : (profile_id, index) Normal BTREE : where (profile_id) order by(index) 를 위함. 일반적인 조회.
  * idx_sa_folders_parent : (parent_id) Normal BTREE : parent_id 로 역탐색 할 때를 위함. 자식노드 탐색할 때 위함.



Categories | 분류 테이블
* 테이블명 : sa_categories
* 설명 : 분류 에 대한 정보 테이블. 분류명에 1:1 매칭이 되도록 함. 
* 컬럼
  * id : (PK) 카테고리 id
  * archive_id : 아카이브 id
  * name : 외부에서 분류명을 중점적으로 탐색하게 됨. unique 를 하거나 pk 를 해야 함. 또는 indexing 을 하거나 해야 함.
  * comments : 부가 설명
  * category (string) : 상위 카테고리. `[분류명][분류명2]` 형태로 입력되는 컬럼.
  * redirect : 넘겨주기가 필요한 경우. 값이 입력되었으면 '넘겨주기 카테고리'로 감안함.
* 인덱스
  * pk 인덱스 : (id)
  * idx_sa_categories_name : (archive_id, name) Normal BTREE
    * name 으로 탐색시 archive_id도 포함해서 해당 카테고리를 찾기에. 속도 향상을 위한 인덱스.



Category x Document | 카테고리 x 문서 릴레이션 테이블
* 테이블명 : sa_category_document_rel
* 목적 : 카테고리에서 하위 문서 탐색을 도와주는 테이블. (문서 테이블에서는 `[분류1][분류2]` 형태로 되어있으므로 이것을 별도의 row로 구성해주고 인덱싱을 해줌)
* 변경 시기 : 문서 작성/문서 변경/문서 삭제에 영향을 받음.
* 컬럼
  * archive_id : (PK) 아카이브 id
  * category_name (string) : (PK) 카테고리명 (한글 가능)
  * document_id : (PK) 문서 id
* 인덱스
  * pk 인덱스 : archive_id, category_name, document_id 순으로 pk 인덱스가 걸려 있음. 
    * archive_id, category_name 으로 document_id 탐색이 잦을 것이므로 이것을 향상시키기 위한 목적.
  * idx_sa_category_document_rel_document : (document_id) Normal BTREE
    * 문서 변경(작성/변경/삭제)시 속도를 향상하기 위한 목적.


Category x relations (분류 x 상위 분류)
* 테이블명 : sa_category_rel (이전 sa_category_parent_rel)
* 설명 : 하위 분류를 탐색하기 위해 생성하는 부분. categories.parent_category 가 수정될 때에 같이 변경해준다. 하나의 값을 레코드로 분류해주어서 검색에 용이하도록 한다.
* 목적 : 하위 분류를 탐색하기 위한 목적. Front 에서는 Ajax 로 호출하게 구성함.
* 컬럼
  * archive_id (PK) : 아카이브 id
  * category_name (string): (PK) 부모 카테고리명 (한글 가능)
  * child_category_name (string) : (PK) 하위 카테고리명 (한글 가능)
* 인덱스
  * pk 인덱스 : (archive_id,category_name,child_category_name)
  * idx_sa_category_rel_child : (archive_id, child_category_name) Normal BTREE
    * 변경(작성/변경/삭제)시 속도 향상


Bookmark | 북마크 테이블
* 테이블명 : sa_bookmarks
* 컬럼
  * profile_id
  * archive_id
  * is_bookmark
  * is_favorite
* 인덱스
  * (profile_id) : 목록을 조회할 때 이용.
  * (archive_id) : 해당 게시물의 북마크 상태를 조회할 때 이용



(Deprecated) Board Tree | 게시판 트리
* 테이블명 : sa_board_tree
* 목적 : 선택한 항목의 하위 게시판 까지 같이 검색할 수 있게 하는 용도.
* 컬럼
  * lft : 좌 범위 포지션
  * rgt : 우 범위 포지션
  * board_id : 게시판 id
* 인덱스
  * sa_board_tree_boardid_index (board_id) Unique BTREE : board_id 로 탐색할 경우에 대한 인덱스.
  * lft 는 pk 인덱스


## View 에 넘기는 변수 

view 에 넘기는 data 형태

* layoutParams : extends 로 호출하는 layout(blade)에서 이용할 변수들.
* bodyParams : body 태그에 추가할 필요가 있는 변수들. (id는 빼고 간략하게 기술. archive, user, folder, category 같이)
* paginationParams : 페이지네이션에 사용되는 변수들.
* links (object) : 버튼 등의 링크에 이용될 주소값들. 
* parameters : 호출 시 넘겨받았던 변수들 혹은 중요값.



# 네이밍 룰
## 데이터베이스

공통
* 전부 소문자 & snake_case 로 한다. (대문자 금지. 가독성 떨어지고 혼란스러움)

세부
* 테이블명 : 'sa_{명칭}' 를 붙인다. (Source Archive 의 약어)
  * 릴레이션 테이블명 : 'sa_category_document_rel' 과 같이 후치사로 '_rel'을 붙인다.
* 인덱스명 : 'idx_{테이블명}_{연관된 컬럼}'
* 프로시저 : 'proc_{동사}_{연관된 테이블이나 컬럼}'
* 함수 : 'func_{동사}_{연관된 테이블이나 컬럼}'

## Laravel/blade, view, js, css

* 폴더명 : '-'로 구분한다. (덜 검색되게 하려고 - 로 구분. 변수명이나 클래스명과 겹치지 않으므로 검색에 용이)
* 파일명 : '_'로 구분한다. (그래야 수정/검색이 편리)


## 변수, 클래스

* 클래스명, 메소드명 : camelCase
* php 의 변수 : camelCase
* 모델에서 테이블의 컬럼과 연관되는 멤버변수는 snake_case 로 한다. (그래야 일괄적 검색이 편리함. 컬럼명이 변경되었을 때 싹 검색할 수 있음)


# 리소스
logo - google material > polymer
icons/folder.svg - octicons > folder open 24
icons/archive.svg - octicons > inbox 24