<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * Http 접속을 Https 로 redirect 시키는 클래스.
 * 클라우드 플레어 자체에서 always https 설정이 되어있으면, 이 기능이 필요하지는 않음. (동일한 기능)
 * 가끔 always https 설정이 꺼질 때가 있으므로 그 때를 대비하는 코드.
 */
class HttpsProtocol {
	// HTTPS 로 제공하지 않을 URI. legacy 로 시작되는 URI 일 경우 SSL 을 강제 적용하지 않음
    protected $except = [
        'legacy/*',
    ];
    // HTTPS 로 제공하지 않을 애플리케이션 환경. 개발자 PC 에서 구동(local)할 경우와 PHPUnit 을 구동(testing)할 경우는 강제 Https 를 사용하지 않음
    protected $exceptEnv = [
        'local',
        'testing',
	];
	
	/**
	 * 참고 주소 https://www.lesstif.com/php-and-laravel/https-27984776.html
	 * http://stackoverflow.com/questions/28402726/laravel-5-redirect-to-https
	 * @param Request $request
	 * @param Closure $next
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function handle($request, Closure $next)
	{
		// Https 가 아니고 제외되는 조건이 아닐 경우 강제로 HTTPS 로 포워딩
		if (!$request->secure() && !$this->shouldPassThrough($request) && !$this->envPassThrough()) {
			return redirect()->secure($request->getRequestUri());
		}
		return $next($request);
	}

	// 제외할 URI 인지 확인
    protected function shouldPassThrough($request)
    {
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return true;
            }
        }
     
        return false;
    }
 
	// 제외할 환경인지 확인
    protected function envPassThrough() 
    {
        $appEnv = \App::environment();
        foreach ($this->exceptEnv as $except) {
            if ($appEnv === $except)
                return true;
        }
        return false;  
	}
	
}