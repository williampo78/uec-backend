<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    private $roleService;
    private $userService;

    public function __construct(
        RoleService $roleService,
        UserService $userService
    ) {
        $this->roleService = $roleService;
        $this->userService = $userService;
    }

    public function showLoginPage()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'account' => 'required',
            'pwd' => 'required',
        ], [
            'account.required' => '使用者帳號不能為空',
            'pwd.required' => '密碼不能為空',
        ]);

        $user = User::where([
            'user_account' => $request->account,
            'user_password' => md5($request->pwd),
        ])
            ->whereNull('supplier_id')
            ->first();

        if (!$user) {
            return redirect('/')
                ->withErrors('帳號或密碼錯誤')
                ->withInput();
        }

        Auth::login($user);
        $this->roleService->putUserRolesSession();
        $this->roleService->setUrlSsoSwitchBtn();
        return redirect()->route('backend_home');
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return Redirect('/');
    }
}
