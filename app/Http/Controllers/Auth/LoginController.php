<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $preUrlSession = 'backUrl';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    public function showLoginForm()
    {
        $backUrl = str_replace(url('/'), '', url()->previous());
        if(!session()->has($this->preUrlSession)){
            session([$this->preUrlSession => $backUrl]);
        } else {
            if($backUrl !='/login')
            {
                session([$this->preUrlSession => $backUrl]);
            }
        }
        return view('auth.login');
    }

    protected function redirectTo(){
    	
    	//$userEmail = $request->input ( 'email' );
    	//echo $userEmail = $this->username();
    	$userEmail = auth()->user()->email;
    	//return;
    	
    	//if($userEmail== 'e2xist@gmail.com'){
    	//	return '/admin';
        //}
        

    	if(!session()->has($this->preUrlSession)){
            return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';    
        } else {
            //$previousUrl = session($this->preUrlSession);
            $previousUrl = session()->pull($this->preUrlSession,'');

            if(preg_match("/\/archives\//",$previousUrl)){
                return $previousUrl;
            }
            if(preg_match("/\/admin\//",$previousUrl)){
                return '/admin';
            }
            if(preg_match("/\/myservice\//",$previousUrl)){
                return $previousUrl;
            }
        }
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
    }
}
