<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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