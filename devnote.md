# 배포시 발생 가능한 케이스
`SQLSTATE[HY000] [1045] Access denied for user 'forge'@'127.0.0.1' (using password: NO)`
* env 설정이 안 되어있거나, config:cache 를 재생성 해야한다. 
* `web/.env`에 설정을 해주고,
* `php artsian config:cache`를 해준다.


net::ERR_HTTP_RESPONSE_CODE_FAILURE (500 오류)
* views 캐싱이 안 되서 발생할 수 있다.
* `chown -R apache:apache storage`


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

