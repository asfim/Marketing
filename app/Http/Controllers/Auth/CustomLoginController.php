<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class CustomLoginController extends Controller
{

    use AuthenticatesUsers;

    protected $redirectTo   = '/dashboard';

    protected $guard        = 'customAuth';


    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $rules  = [
            'userEmail'     => 'required|email',
            'password'  => 'required'
        ];

        $this->validate($request, $rules);
        $errors = new MessageBag(['password' => ['Wrong Login Name Or Password!']]);

        $auth   = Auth::guard($this->guard)->attempt(['email' => $request->input('userEmail'), 'password' => $request->input('password')]);

        if($auth)
        {
            return redirect()->intended(route('admin.home'));
        }
        return redirect()->back()->withInput()->withErrors($errors);
    }

    public function logout() {
        Auth::guard('customAuth')->logout();
        return redirect()->route('login');
    }
}
