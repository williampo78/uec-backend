<?php

namespace App\Http\Middleware;

use App\Services\RoleService;
use App\Services\UserService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticate
{
    private $userService;
    private $roleService;

    public function __construct(
        UserService $userService,
        RoleService $roleService
    ) {
        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 未登入，跳轉到登入頁
        if (!auth()->check()) {
            return redirect()->guest(route('login.show'));
        }

        // 未設定導覽列session
        if (!session()->has('dradvice_menu')) {
            $this->userService->setMenuSession();
        }

        //顯示頁面權限，無權限將被導至首頁
        $roleAuth = $this->roleService->getDisplayRoles();
        // 沒有權限
        if ($roleAuth != 1) {
            $result = [];
            $result['message'] = '你沒有權限執行這個操作！';
            $result['error_code'] = '';

            return response()->view('backend.error', $result);
        }

        //其他權限，顯示按鈕及能否增修刪查匯出等
        $otherRoleAuth = $this->roleService->getOtherRoles();
        //權限參數傳入view, 可以直接在各個view取用
        view()->share('share_role_auth', $otherRoleAuth);
        view()->share('share_type_file','file');
        // 權限參數傳入Request物件
        $request->merge(["share_role_auth" => $otherRoleAuth]);

        return $next($request);
    }
}
