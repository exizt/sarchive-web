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


# 개발 환경
* Laravel 7.0 이상
* PHP 7.4 이상 (요구사항 7.2.5 이상)


Laravel 에서 필요한 PHP 구성

* extension=openssl : 뭐였는지 기억 안 나지만 필요함
* extension=pdo_mysql : DB 연결을 위해 필요
* extension=mbstring : 뭐였는지 기억 안 남
* extension=fileinfo : 파일업로드 기능을 위해 필요한 듯

