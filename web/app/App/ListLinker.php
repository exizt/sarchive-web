<?php
namespace App\App;

use Illuminate\Http\Request;

/**
 *
 * 컨텐츠 목록 화면
 *    - (1) 신규 버튼 : category, board, folder 등의 목록으로 돌아가기 위한 링크 추적 파라미터를 URL로 넘긴다.
 *    - (2) 컨텐츠 링크 : page 파라미터 + (category, board, folder 등 목록의 파라미터)
 *
 * 컨텐츠 보기 화면
 *    - (1) 목록/취소/뒤로
 *        - Case 1. 목록에서 넘어왔을 때: history.back()으로 충분하다.
 *        - Case 2. 글 편집 후 넘어왔을 때: '목록'링크를 생성해서 넘긴다. 인스턴스 세션(flash 세션)이 활용된다. 직전 주소(편집 화면 주소)를 활용한다.
 *        - Case 3. 외부 접속으로 넘어왔을 때 : history.back()이 되어서는 안 된다. 다른 사이트 주소로 돌아가버리기 때문.
 *    - (2) 편집 버튼 : page 파라미터 + (category, board, folder 등 목록의 파라미터)
 *    - 링크 추적 파라미터
 *        - category, board, folder 등의 목록으로 돌아가기 위한 파라미터는 필요하다.
 *        - page 파라미터도 필요하다.
 *
 * 컨텐츠 신규 작성 화면
 *    - (1) 목록/취소/뒤로 : history.back()으로 충분하다.
 *    - (2) 저장 후 리디렉션 : 저장 후에는 해당 컨텐츠의 보기 화면으로 넘어간다. or 목록 첫 화면으로 넘어간다.
 *    - 특기 사항
 *        - 외부에서 접속 시 : 차단.
 *        - 파라미터 : 작성에 필요한 파라미터는 받을 필요가 있다. 예를 들어 카테고리, 폴더 등의 작성에 도움이 되는 정보이다.
 *    - 링크 추적 파라미터
 *        - category, board, folder 등의 목록의 처음으로 돌아가기 위한 파라미터는 필요하다.
 *
 * 컨텐츠 수정 화면
 *    - (1) 목록/취소/뒤로
 *        - Case 1. 문서 화면에서 넘어왔을 때 : history.back()
 *        - Case 2. '저장 후 계속 편집' 중일 때 : 링크를 생성해야 함.
 *    - (2) 저장 후 리디렉션 : 컨텐츠 화면으로 이동한다.
 *    - 특기 사항
 *        - 외부에서 접속 시 : 차단.
 *        - GET URL에 링크 추적을 위한 파라미터를 물고 있어야 한다. 그래야 저장 후 컨텐츠 화면으로 갔을 때, 목록 링크를 살릴 수 있다.
 *    - 링크 추적 파라미터
 *        - category, board, folder 등의 목록으로 돌아가기 위한 파라미터는 필요하다.
 *        - page 파라미터도 필요하다.
 */
class ListLinker {

    /**
     * Request의 Get 파라미터 변수 중 링크에 활용되는 변수에 대해서
     * 파라미터 배열을 생성.
     *
     * @param Request $request
     * @param array $allowed_keys
     * @param boolean $int_filter
     * @return array
     */
    static public function getLinkParameters(Request $request, array $allowed_keys, bool $int_filter=false): array
    {
        if( empty($allowed_keys) ){
            return [];
        }

        // 단순 배열인지, 키 밸류 배열인지
        $key_is_list = array_is_list($allowed_keys);

        // 키 값만 추출. (key-value형일 수도 있어서)
        $_keys = ($key_is_list) ? $allowed_keys: array_keys($allowed_keys);

        $parameters = array();
        foreach($_keys as $_key){
            $originKey = ($key_is_list) ? $_key: $allowed_keys[$_key];

            $value = $request->input($originKey);
            if(!empty($value)){
                if($int_filter){
                    $parameters[$_key] = (int) $value;
                } else {
                    $parameters[$_key] = $value;
                }
            }
        }
        return $parameters;
    }

    /**
     * 배열을 http query string으로 변환하는 함수
     */
    static public function makeQueryString(array $arr): string{
        // http_build_query: array -> http query string으로 변환시켜주는 PHP 함수
        // https://www.php.net/manual/en/function.http-build-query.php
        return http_build_query($arr);
    }

    /**
     * url 문자열에서 파라미터만 추출하는 함수
     */
    static public function getParametersFromUrl(string $url, array $allowed_keys, bool $int_filter=false): array
    {
        // parse_url: 전체 url에서 query string 부분만 추출하는 PHP 함수
        // https://www.php.net/manual/en/function.parse-url.php
        $parts = parse_url($url);
        return self::getParametersFromQueryString($parts['query'], $allowed_keys, $int_filter);
    }

    /**
     * http query string 문자열에서 파라미터만 추출하는 함수
     */
    static private function getParametersFromQueryString(string $query_string, array $allowed_keys, bool $int_filter=false): array
    {
        if( empty($query_string) || empty($allowed_keys) ){
            return [];
        }

        // parse_str: query string을 배열로 변환하눈 PHP 함수
        // https://www.php.net/manual/en/function.parse-str.php
        $queryParams = array();
        parse_str($query_string, $queryParams);

        // $parameters = array_intersect_key($parameters, array_flip($allowed_keys));
        // 단순 배열인지, 키 밸류 배열인지
        $key_is_list = array_is_list($allowed_keys);

        // 키 값만 추출. (key-value형일 수도 있어서)
        $_keys = ($key_is_list) ? $allowed_keys: array_keys($allowed_keys);

        $parameters = array();
        foreach($_keys as $_key){
            $originKey = ($key_is_list) ? $_key: $allowed_keys[$_key];

            // $value = $request->input($originKey);

            $value = $queryParams[$originKey] ?? null;
            if(!empty($value)){
                if($int_filter){
                    $parameters[$_key] = (int) $value;
                } else {
                    $parameters[$_key] = $value;
                }
            }
        }

        return $parameters;
    }
}


// PHP 8.1에 추가되었다고 함.
// https://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
if (!function_exists('array_is_list')) {
    function array_is_list(array $arr)
    {
        if ($arr === []) {
            return true;
        }
        return array_keys($arr) === range(0, count($arr) - 1);
    }
}
