<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

/**
 * 로그인이 된 경우에 보여주고 아닌 경우에는 404 not found 처리
 * 
 * Authenticate에서 조금 변경. 
 * 
 * @author shoon
 */
class AuthenticateWithNotFound extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        abort(404);
    }
}
