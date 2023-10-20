# TODO
SArchive 프로젝트 TODO
* jquery 제거
    * treeJS의 4 버전이 개발중인데 jquery를 사용하지 않았다. 이를 이용해볼 것.
*  디자인 레이아웃
    * tailwind 검토해 볼 것
    * bootstrap 5로 변경
        * 관리자 레이아웃하고 일반 레이아웃하고 하나로 사용하는데, 이것을 분리할 것.
        * 관리자 레이아웃에서 bs 5를 먼저 적용시키고, 아카이브 레이아웃에는 차차 적용시켜 나갈 것.
- 검색 기능 강화
    - 카테고리별 검색이 되도록 할 것.
- 폰트 가독성 : 더 좋은 폰트를 찾아보자.
- 게시판 삭제 시에 기존 게시물이 어디로 이동할지...
- 북마크 목록.
- 테마 설정 가능하게
- 설정에서 폰트/폰트크기 변경 가능하게.
- 카테고리 기능을 그냥 없애도 될 거 같은데?
- 페이지 기능도 없애도 될 거 같고.
- 뷰 처리
    - 테마 기능처럼 뷰를 변경가능하게 고려할 것. 변경이 좀 더 쉽게 하는 것이 목적.


jquery 코드가 남아있는 곳들
- admin/folder-control/index.blade.php : jstree가 사용되어야 하므로 어쩔 수가 없음.
- app/explorer/folder-selector.blade.php : jstree가 사용되어야 하므로 어쩔 수가 없음.
- app/folder/_form.blade.php : 완료. (modal만 남음)
- app/document/_form.blade.php : 완료 (modal만 남음)





## 완료됨
- 우측 네비게이션에 jquery 구문 제거.
- 입력폼에서 엔터키 방지 부분을 jquery에서 바닐라 코드로 변경 (2023-10)


# 기획, 구성
## 사용되는 URL 목록
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

## 코드 구성
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
