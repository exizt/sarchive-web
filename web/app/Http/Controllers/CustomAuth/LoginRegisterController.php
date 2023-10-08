<?php

namespace App\Http\Controllers\CustomAuth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginRegisterController extends Controller
{
    protected $blade_dir = 'custom_auth';
    
    /**
     * Instantiate a new LoginRegisterController instance.
     */
    public function __construct()
    {
        // 세션 체크에서 제외되는 항목 (logout)
        $this->middleware('guest')->except([
            'logout'
        ]);
    }

    /**
     * 사용자 등록 폼
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        return view($this->blade_dir.'.register');
    }

    /**
     * 사용자 등록 -> 저장
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $credentials = $request->only('email', 'password');
        
        Auth::attempt($credentials);
        
        $request->session()->regenerate();
        
        return redirect(RouteServiceProvider::HOME);
        // return redirect()->route('dashboard')
        // ->withSuccess('You have successfully registered & logged in!');
    }

    /**
     * 사용자 로그인 폼
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view($this->blade_dir.'.login');
    }

    /**
     * 사용자 로그인 -> 인증
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials))
        {
            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::HOME);
            # return redirect()->route('dashboard')
            #    ->withSuccess('You have successfully logged in!');
        }

        return back()->withErrors([
            'email' => 'Your provided credentials do not match in our records.',
        ])->onlyInput('email');

    }
    
    /**
     * 로그아웃
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        
        $request->session()->regenerateToken();
        
        return redirect('/');

        //return redirect()->route('login')
        //    ->withSuccess('You have logged out successfully!');
    }
}
