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



# URLs
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



# 개발 메모
컨트롤러 추가 시. 

* php artisan make:controller PhotoController --resource
* php artisan make:controller Admin/PhotoController --resource



모델 추가 시

* php artisan make:model --migration Post
* php artisan make:model --migration Models/Post
