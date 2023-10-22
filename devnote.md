# TODO

Todo 단기
* 


중단기 Todo
-  디자인 및 레이아웃
    - bootstrap에서 빠져나오기
        - tailwind 검토해 볼 것
        - navbar에 대한 구현이 필요함
    - 폰트 가독성 : 더 좋은 폰트를 찾아보자.
- 검색 기능 강화
    - 카테고리별 검색이 되도록 할 것.
- 게시판 삭제 시에 기존 게시물이 어디로 이동할지...
- 테마 설정 가능하게 : 컬러 테마 정도로만.
    - 다크 테마
- 카테고리 기능을 그냥 없애도 될 거 같은데? (검토중..)







## 완료됨
- 페이지 기능 없앰 : `sa_pages` 테이블 삭제 및 모델, 마이그레이션 삭제
- 우측 네비게이션에 jquery 구문 제거. (2023-10)
- 입력폼에서 엔터키 방지 부분을 jquery에서 바닐라 코드로 변경 (2023-10)
- Bootstrap 4에서 5로 업그레이드 (2023-10)
- jquery 제거 : 대부분 제거함 (2023-10)
    - jquery 코드가 남아있는 곳들
        - admin/folder-control/index.blade.php : jstree가 사용되어야 하므로 어쩔 수가 없음.
        - app/explorer/folder-selector.blade.php : jstree가 사용되어야 하므로 어쩔 수가 없음.



passed
- view를 theme으로 구분하기 => 관리자 페이지까지 얽혀있어서 무리가 있을 듯함.
- 설정에서 폰트/폰트크기 변경 가능하게 => 그냥 브라우저에서 폰트 크기를 조절하는 게 나을 듯함.
- jquery 제거
    - treeJS의 4 버전이 개발중인데 jquery를 사용하지 않았다. 이를 이용해볼 것 => 난해해서 사용 못하겠음.


# 구성
## URL 구성
URL 구성 (접속 가능한 URL)
* `/` : 메인 페이지
    * 기본으로 설정된 아카이브 첫 화면으로 리디렉션.
* 아카이브
    * `/archives/{archive_id}` : 아카이브의 첫 화면. '문서 추가', '탐색' 버튼과 '검색 기능'
    * `/archives/{archive_id}/latest` : 아카이브의 최근 게시물.
* 문서
    * `/doc/{document_id}` : 문서 보기 화면.
    * `/archives/{archive_id}/doc/create` : 새 문서 화면.
    * `/doc/{document_id}/edit` : 문서 편집 화면.
* 폴더, 카테고리, 탐색기
    * 폴더
        * `/folders/{folder_id}` : 해당 폴더의 최근 게시물. (하위 폴더 포함)
        * `/folders/{folder_id}?only=1` : 해당 폴더에만 속한 최근 게시물.
    * 검색
        * `/archives/{archive_id}/search?q={keyword}` : 검색 결과 페이지.
    * 카테고리
        * `/archives/1/category/{category_name}` : 해당 카테고리의 최근 게시물.


## 코드 폴더 구성
Controllers (`web/app/Http/Controllers/`)
* `Admin/` : 관리자 모드 관련
* `Archive/` : 아카이브 관련
* `Auth/` : 라라벨에서 추가된 인증 관련
* _`CustomAuth/`_ : 심플 로그인 (로컬에서만 사용됨)


Views (`web/resources/views/`)
* `admin/` : 관리자 모드 관련
* `app/` : SArchive 관련 뷰.
* _`custom_auth/`_ : 심플 로그인 (로컬에서만 사용됨)
* `layouts/` : 레이아웃 구성
* `modules/` : modal, messages 등
* framework 관련
    * ~~`auth/`~~ : 라라벨에서 추가된 인증 관련
    * ~~`components/`~~ : 라라벨에서 추가된 컴포넌트들
    * _`vendor/`_ : vendor에서 추가된 것들. pagination 등.

