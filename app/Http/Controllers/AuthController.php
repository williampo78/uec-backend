<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\Request;
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

    /**
     * 登入頁
     */
    public function showLoginPage()
    {
        return view('backend.auth.login');
    }

    /**
     * 登入
     */
    public function login(Request $request)
    {
        $request->validate([
            'account' => 'required',
            'pwd' => 'required',
            'captcha' => 'captcha',
        ], [
            'account.required' => '使用者帳號不能為空',
            'pwd.required' => '密碼不能為空',
            'captcha.captcha' => '驗證碼錯誤',
        ]);

        $user = User::where([
            'user_account' => $request->account,
            'user_password' => md5($request->pwd),
        ])
            ->whereNull('supplier_id')
            ->first();

        if (!$user) {
            return back()
                ->withErrors(['帳號或密碼錯誤'])
                ->withInput();
        }

        Auth::login($user);
        $this->roleService->putUserRolesSession();
        $this->roleService->setUrlSsoSwitchBtn();

        return redirect()->intended(route('backend_home'));
    }

    /**
     * 登出
     */
    public function logout()
    {
        Session::flush();
        Auth::logout();

        return redirect()->route('login.show');
    }
}
