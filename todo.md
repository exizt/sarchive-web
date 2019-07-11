# To-Do
* 

# URLs
/archives/[게시물번호]

/category/[카테고리명(한글가능)]

* CategoryController

/page/[페이지명(한글가능)]

* PageController

/board/[게시판번호]




# 개념 

문서 document
* 문서 게시물의 단위. 

페이지 page
* 웹 페이지. 개별적으로 작성되는 웹페이지.

분류 category
* 문서들을 묶어놓는 분류. 다층형 카테고리 개념.

게시판 board
* 과거의 카테고리 개념. 상위 게시판 개념이 존재함. 트리형 카테고리 개념.



# 레이아웃
상단에 검색 폼은 항상 나오도록 해야 함. 검색이 가장 기본이 되도록 함.


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
profiles | 프로필 테이블
* 프로필 테이블
* 테이블명 : sa_profiles
* 설명 : 유저에 대응되어서 생성된 아카이브의 구분값. 아카이브를 여러개 생성할 수 있게 해주는 것.
* 컬럼 
  * id
  * user_id
  * name
  * text : 설명 텍스트 등.


archives | 아카이브 테이블
* 테이블명 : sa_archives
* 설명 : 글 내용. 
* 참고) 차후에 다중 구성을 할 수 있음. (탭 구성으로 해서 엑셀 문서 처럼...)
* 컬럼
  * id
  * title
  * content
  * summary
  * unit_code
  * board_id
  * summary_var
  * reference
  * category : [분류명][분류명2]
* 인덱스
  * fulltext_index (title, content) 'Full Text' : 검색용 인덱스
  * sa_archives_latest_select_index : (board_id, created desc) 인덱스. 카테고리별로 정렬된 인덱스. 목록 불러올 때 이용되는 인덱스.


categories | 분류 테이블
* 테이블명 : sa_categories
* 설명 : 분류 에 대한 정보 테이블. 분류명에 1:1 매칭이 되도록 함. 
* 컬럼
  * id : 카테고리 ID
  * profile_id : 아카이브 구분값.
  * name : 외부에서 분류명을 중점적으로 탐색하게 됨. unique 를 하거나 pk 를 해야 함. 또는 indexing 을 하거나 해야 함.
  * parent : 상위 분류 설정 값. [분류1] [분류2] 와 같은 형태로..
  * redirect : 넘겨주기가 필요한 경우. 값이 입력되었으면 '넘겨주기 카테고리'로 감안함.
  * text : 분류에 대한 설명 txt 
* 인덱스 
  * sa_categories_profile_index : (profile_id, name) Normal BTREE, profile_id 과 name 로 접근하게 될 검색을 예견.


category_parent (분류 x 상위 분류)
* 테이블명 : sa_category_parent_rel
* 설명 : 하위 분류를 탐색하기 위해 생성하는 부분. categories.parent_category 가 수정될 때에 같이 변경해준다. 하나의 값을 레코드로 분류해주어서 검색에 용이하도록 한다.
* 목적 : 하위 분류를 탐색하기 위한 목적. Front 에서는 Ajax 로 호출하게 구성함.
* 컬럼
  * id : 크게 중요하지 않음. auto increment
  * profile_id : 아카이브 구분값.
  * parent (string): 분류명 (한글 가능)
  * child (string) : 하위 분류명 (한글 가능)
* 인덱스
  * sa_category_parent_index : (profile_id,parent,child) Normal BTREE


archive category relations 아카이브 카테고리 릴레이션 테이블 (아카이브 X 카테고리)
* 테이블명 : sa_category_archive_rel
* 목적 : 분류에서 하위 문서를 탐색하기 위한 목적.
* 설명 : 글 작성/변경 시기에 필요하면 갱신한다. archives.category 부분에 영향을 받는다. 
* 컬럼
  * id : 크게 중요하지 않음. auto increment
  * profile_id : 아카이브 구분값.
  * category (string) : 분류명 (한글 가능)
  * archive_id : 분류에 해당되는 아카이브의 id

  

sa_boards
* 컬럼
  * id 
  * name : 명칭
  * parent_id : 상위 게시판 id
  * comment : 부가 설명
  * count : 해당 게시판의 게시글 수
* 인덱스
  * sa_board_parent_index : (parent_id) Normal BTREE


sa_board_tree


# 네이밍 룰
## 데이터베이스

테이블명 
* prefix 로 'sa_' 를 붙인다. sa 는 Source Archive 의 약어.


인덱스명
* prefix 로는 '테이블명_' 을 동일하게 한다. 차후에 혼동을 방지하는 것이 목적.
* suffix 로는 '_index' 를 붙인다. 인덱스 개체임을 명시하기 위함.
* 중간값은 무난하다면 키 값으로 한다. 명칭 중복이 예견된다면 독특한 명을 사용하거나 숫자를 추가적으로 붙인다.

