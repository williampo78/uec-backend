<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class LoginAuthController extends Controller
{

    public function index()
    {
        if (!Auth::check()) {
            return view('login');
        } else {
            return Redirect('backend');
        }
    }

    public function customLogin(Request $request)
    {
        $request->validate([
            'account' => 'required',
            'password' => 'required',
        ], [
            'account.required' => '使用者帳號不能為空',
            'password.required' => '密碼不能為空',
        ]);
        $user = User::where([
            'account' => $request->account,
            'password' => md5($request->password),
        ])->first();

        if ($user) {
            Auth::login($user);
            return redirect()->intended('backend')
                ->withSuccess('Signed in');
        } else {
            return redirect('/')
                ->withErrors('帳號或密碼錯誤')
                ->withInput();
        }
    }

    public function registration()
    {
        return view('auth.registration');
    }

    public function customRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'account' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        $data = $request->all();
        $check = $this->create($data);

        return redirect("dashboard")->withSuccess('You have signed-in');
    }

    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function dashboard()
    {
        if (Auth::check()) {
            return view('Backend.main');
        }

        return redirect("login")->withSuccess('You are not allowed to access');
    }

    public function signOut()
    {
        Session::flush();
        Auth::logout();

        return Redirect('/');
    }
}
