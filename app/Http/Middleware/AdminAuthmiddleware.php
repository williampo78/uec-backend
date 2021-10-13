<?php

namespace App\Http\Middleware;
use App\Services\RoleService;
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
        $roleService = new RoleService;
        $role_auth = $roleService->getDisplayRoles();
        $other_role_auth = $roleService->getOtherRoles();
        //權限參數傳入view, 可以直接在各個view取用
        view()->share('share_role_auth', $other_role_auth);

        if(Auth::check()){
            //DB role_permission_details 資料尚未建齊, 暫時先註解此功能
//            if ($role_auth ==1){
                return $next($request);
//            }else{
//                return redirect()->route('admin.index');
//            }
        }else{
            return Redirect('/');
        }
    }
}
