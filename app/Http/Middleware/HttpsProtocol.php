<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Http 접속을 Https 로 redirect 시키는 클래스.
 * 클라우드 플레어 자체에서 always https 설정이 되어있으면, 이 기능이 필요하지는 않음. (동일한 기능)
 * 가끔 always https 설정이 꺼질 때가 있으므로 그 때를 대비하는 코드.
 */
class HttpsProtocol {
	/**
	 * 참고 주소 http://stackoverflow.com/questions/28402726/laravel-5-redirect-to-https
	 * @param Request $request
	 * @param Closure $next
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function handle($request, Closure $next)
	{
		
		// 개발 모드가 아닌 경우에. 필요에 따라 https 설정을 할 것.
		if (!app()->environment('local')) {
			// for cloudflare (this will stop the redirect loop)
			$request->setTrustedProxies( [ $request->getClientIp() ] );
			// app_env check for cloudflare
			if (!$request->secure()) {
				return redirect()->secure($request->getRequestUri());
			}
			
		}
		return $next($request);
	}
}