# 개요 
자세한 내용은 todo.md 를 참조할 것.

* 위키 문서 링크 : [링크](https://swiki.asv.kr/wiki/나의 프로젝트/S아카이브)



# 사이트 내의 단축키

1. [C] : 뒤로가기
2. [S] : (일반적으로) 저장
3. [F] : 검색
4. [N] : 새로 글 쓰기



# URL & Class 명 룰
Case 1) 데이터를 조회하고, 데이터베이스와 직연결된 서비스의 경우 
예시)
* 컨트롤러 : Controllers.Services.ArchiveController
* 모델 : Models.Archive
* 뷰 : services.archive.index, services.archive.show, service.archive.edit, service.archive.create
* 테이블 : Archives
* URL : /archive, /archive/{number}, /archive/{number}/edit, /archive/create



# php 에서 필요한 것
* extension=openssl : 뭐였는지 기억 안 나지만 필요함
* extension=pdo_mysql : DB 연결을 위해 필요
* extension=mbstring : 뭐였는지 기억 안 남
* extension=fileinfo : 파일업로드 기능을 위해 필요한 듯


# 개발 메모
컨트롤러 추가 시. 

* php artisan make:controller PhotoController --resource
* php artisan make:controller Admin/PhotoController --resource



모델 추가 시

* php artisan make:model --migration Post
* php artisan make:model --migration Models/Post
