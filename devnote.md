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
