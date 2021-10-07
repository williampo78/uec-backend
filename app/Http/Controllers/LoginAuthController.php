<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginAuthController extends Controller
{

    public function index()
    {
        if(!Auth::check()){
            return view('login');
        }else{
            return Redirect('backend');
        }
    }


    public function customLogin(Request $request)
    {
        $request->validate([
            'account' => 'required',
            'password' => 'required',
        ]);

        $user = User::where([
            'account'  => $request->account,
            'password'  => md5($request->password)
        ])->first();

        if ($user) {
            Auth::login($user);

            return redirect()->intended('backend')
                ->withSuccess('Signed in');
        }

        return redirect("login")->withSuccess('Login details are not valid');
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
            'password' => Hash::make($data['password'])
        ]);
    }


    public function dashboard()
    {
        if(Auth::check()){
            return view('Backend.main');
        }

        return redirect("login")->withSuccess('You are not allowed to access');
    }


    public function signOut() {
        Session::flush();
        Auth::logout();

        return Redirect('/');
    }
}
