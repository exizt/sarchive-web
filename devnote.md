# 문서 개요 
> SArchive를 개발하기 위한 정보에 대한 모음.


# TODO
Todo 단기
- navbar에 대한 css 구현이 필요.
- 

<br>

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


# 개발 환경 및 개발 작업
## 개발 환경 셋팅

> `readme.md`를 참조하여 로컬 환경에 셋팅을 먼저 진행한다. 그 이후의 작업이다.


1. 개발 도구 : VSCode
2. 필요한 것 : Docker, Docker-Compose
3. VSCode 개발 환경에서 필요한 익스텐션
    - PHP Intelephense (`bmewburn.vscode-intelephense-client`): PHP 구문 인텔리전스.
    - CSS 관련
        - Live Sass Compiler (`glenn2223.live-sass`): 필수. scss -> css
    - Laravel 관련
        - Laravel Snippets (`onecentlin.laravel5-snippets`): Laravel 구문 인텔리전스.
        - Laravel Blade Snippets (`onecentlin.laravel-blade`): Laravel blade 구문 인텔리전스.
        - Laravel Extra Intelilisense (`amiralizadeh9480.laravel-extra-intellisense`): Laravel 구문 인텔리전스.


개발 환경 셋팅 과정
1. VSCode로 프로젝트를 연다.
2. VSCode 확장 기능을 적절히 셋팅한다.
3. docker 명령으로 컨테이너를 실행시키고, 작업을 한다.


### 셋팅 직후 추가 작업
작업의 편의를 위해 다음의 심볼릭 링크를 생성한다.
```shell
ln -s ./larabasekit/scripts/dev/cmd-web.sh local.sh
```
* 로컬에서 명령어를 수행하기 쉽게 해주는 스크립트. 웹 컨테이너로 명령어를 전달한다.


사용법 예시
```shell
./local.sh "ls -al"
./local.sh "composer --version"
```



## 릴리즈 배포 및 버전 관리
1. `web/config/_app.php`에서 버전 정보 수정 후 커밋
2. 버전은 `v23.1` (`v`+`연도.`+`증분숫자`)의 형식으로 함.


## 기타 정보



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


## 폴더 구성
(메인 폴더 구성)
- `larabasekit` : 라라벨 프로젝트 공통으로 쓰이는 스크립트 모음 (submodule)
- `scripts` : 주로 사용되는 스크립트.
- `web` : 라라벨 소스 코드.


### 코드 폴더 구성
Controllers (`web/app/Http/Controllers/`)
- `Admin/` : 관리자 모드 관련
- `Archive/` : 아카이브 관련
- ~~`Auth/`~~ : 라라벨에서 추가된 인증 관련
- ~~`CustomAuth/`~~ : 심플 로그인 (로컬에서만 사용됨)


Views (`web/resources/views/`)
- `admin/` : 관리자 모드 관련
- `app/` : SArchive 관련 뷰.
- ~~`custom_auth/`~~ : 심플 로그인 (로컬에서만 사용됨)
- `layouts/` : 레이아웃 구성
- `modules/` : modal, messages 등
- framework 관련
    - ~~`auth/`~~ : 라라벨에서 추가된 인증 관련
    - ~~`components/`~~ : 라라벨에서 추가된 컴포넌트들
    - `vendor/` : vendor에서 추가된 것들. pagination 등.
