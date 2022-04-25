<?php

namespace App\Http\Middleware;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;

class AdminAuthmiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 未登入，轉址到登入頁
        if (!auth()->check()) {
            return redirect()->route('login.show');
        }
        if(!session('dradvice_menu')){
            $userService = new UserService ;
            $userService->setMenuSession();
        }
        $roleService = new RoleService;
        //顯示頁面權限，無權限將被導至首頁
        $roleAuth = $roleService->getDisplayRoles();
        //其他權限，顯示按鈕及能否增修刪查匯出等
        $otherRoleAuth = $roleService->getOtherRoles();
        //權限參數傳入view, 可以直接在各個view取用
        view()->share('share_role_auth', $otherRoleAuth);

        // 沒有權限
        if ($roleAuth != 1) {
            $result = [];
            $result['message'] = '你沒有權限執行這個操作！';
            $result['error_code'] = '';

            return response()->view('backend.error', $result);
        }

        return $next($request);
    }
}
