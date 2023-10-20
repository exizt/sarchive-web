<?php
namespace App\App;

use Illuminate\Http\Request;

class ListLinker {

    /**
     * Request의 Get 파라미터 변수 중 링크에 활용되는 변수에 대해서
     * 파라미터 배열을 생성.
     *
     * @return array
     */
    /**
     * @param Request $request
     * @param array $allowed_keys
     * @param boolean $int_filter
     */
    static public function getLinkParameters(Request $request, array $allowed_keys, bool $int_filter=false): array {
        $parameters = array();

        // 단순 배열인지, 키 밸류 배열인지
        $key_is_list = array_is_list($allowed_keys);

        // 키 값만 추출. (key-value형일 수도 있어서)
        $_keys = ($key_is_list) ? $allowed_keys: array_keys($allowed_keys);

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
    static private function makeQueryString(array $arr): string{
        // http_build_query: array -> http query string으로 변환시켜주는 PHP 함수
        // https://www.php.net/manual/en/function.http-build-query.php
        return http_build_query($arr);
    }

    /**
     * url 문자열에서 파라미터만 추출하는 함수
     */
    static private function getParametersFromUrl(string $url, array $allowed_keys=null): array{
        // parse_url: 전체 url에서 query string 부분만 추출하는 PHP 함수
        // https://www.php.net/manual/en/function.parse-url.php
        $parts = parse_url($url);
        return self::getParametersFromQueryString($parts['query'], $allowed_keys);
    }

    /**
     * http query string 문자열에서 파라미터만 추출하는 함수
     */
    static private function getParametersFromQueryString(string $query_string, array $allowed_keys=null): array{
        // parse_str: query string을 배열로 변환하눈 PHP 함수
        // https://www.php.net/manual/en/function.parse-str.php
        $parameters = array();
        if( !empty($query_string) ){
            parse_str($query_string, $parameters);

            if( !empty($allowed_keys) ){
                $parameters = array_intersect_key($parameters, array_flip($allowed_keys));
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
