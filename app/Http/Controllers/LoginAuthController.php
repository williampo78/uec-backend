<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginAuthController extends Controller
{
    private $roleService;
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

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
        $users = Users::where([
            'user_account' => $request->account,
            'user_password' => md5($request->password),
        ])->first();

        if ($users) {
            Auth::login($users);
            $this->roleService->putUserRolesSession();

            return redirect()->route('backend-home')
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
