<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:customer', ['except' => ['logout']]);
    }

    public function login()
    {
        return view('customer.auth.login');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('customer')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            Toastr::success('Welcome back!');
            return redirect()->route('customer.dashboard');
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        return redirect()->route('customer.auth.login');
    }
}
