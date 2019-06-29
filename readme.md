# 개요 
* 서비스 주소로 : [링크](https://chosim.asv.kr/archives)
* 위키 문서로 (SWIKI/프로젝트/S 아카이브 문서) : [링크](https://swiki.asv.kr/wiki/%ED%94%84%EB%A1%9C%EC%A0%9D%ED%8A%B8/S_%EC%95%84%EC%B9%B4%EC%9D%B4%EB%B8%8C)





# 사이트 내의 단축키

1. [C] : 뒤로가기
2. [S] : (일반적으로) 저장
3. [F] : 검색
4. [N] : 새로 글 쓰기


# 구성
## 주요 구성
1. 아카이브 (archieve) : 자료 저장 기록소
2. 제품 지원 (products) : 제품 설명, 다운로드, 개인정보 처리 방침 등
3. 블로그 (blog) : 내 블로그. 
4. 각종 계산기 들 (Calculators) : 대출이자 계산기, 연봉 계산기, 전기세 계산기 등



## 목적별 구성 분류
1. 사이트 관리자
    - 사이트 관리, 통계 분석, 블로그 포스팅 등
    - 사이트 관리 기능 들을 말한다.
2. 마이 서비스
    - 나만 쓸 서비스. 소스 저장소 등.
    - 나만 사용되는 기능 들을 말한다.
3. 유저 서비스
    - 유저별로 제공될 만한 서비스. 비밀번호 변경, 할일 관리 등
    - 사용자 (유저, 비유저 접속자) 에게 제공되어지는 기능 들을 말한다.
    - 각종 계산기들
    - 제품 지원

## 유닛 구성
* Services 서비스들 : 큰 단위의 기능들
    - 아카이브 Archieve
    - 블로그 Blog
* Admin 사이트 관리자 : 사이트 관리, 통계 분석, 블로그 포스팅 등
* UserServices 유저 서비스들 : 접속자 (유저 또는 비유저) 가 사용하게 만든 기능들
* Products 제품들 : 내가 만든 Software 등
* MyServices : 나만 단독으로 사용할 기능들, 테스트 중인 기능들.



# URL & Class 명 룰
Case 1) 데이터를 조회하고, 데이터베이스와 직연결된 서비스의 경우 
예시)
* 컨트롤러 : Controllers.Services.ArchiveController
* 모델 : Models.Archive
* 뷰 : services.archive.index, services.archive.show, service.archive.edit, service.archive.create
* 테이블 : Archives
* URL : /archive, /archive/{number}, /archive/{number}/edit, /archive/create

Case 2) 단순 기능, 계산기와 같은 경우
 예시)
* 컨트롤러 : Controllers.Services.Calculators.SalarayCalculatorController
* 뷰 : services.calculators.salary.index, services.calculators.salary.show, ..
* 클래스 : System.Calculators.
* URL : services/loan-calculator
* 모델 : X 
* 테이블 : X



# 각 구성
## 아카이브
조건
1) 특정 아이피는 로그인 없이 조회가 가능함.
2) 지정 아이피 외에는 로그인 되어야 조회가 가능함

내용
1) 스크랩 한 글 들을 복사 붙여넣기 하는 공간.
2) 두서 없이 마구 데이터를 넣어둔다. 

고려 사항
1) 카테고리 명칭 변경이 가능해야 한다.
2) 글의 카테고리 변경이 가능해야 한다.
3) 여러 글을 선택해서 카테고리 변경이 가능해야 한다.
4) 개발 관련 과 비개발 관련은 아예 나누어 둔다.


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



# 가이드라인

공통적인 것은 Common 패키지 이하로 넣습니다. 
* 예시) App/Http/Controllers/Common/
* 예시) App/Models/Common/



웹 구성은 

















