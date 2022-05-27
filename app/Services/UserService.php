<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class UserService
{
    public function getUsers($data = [])
    {
        $authUser = Auth::user();

        $users = User::with(['updatedByUser'])
            ->where('agent_id', $authUser->agent_id);

        if (isset($data['active'])) {
            $users->where('active', $data['active']);
        }

        if (isset($data['user_account'])) {
            $users->where('user_account', 'like', '%' . $data['user_account'] . '%');
        }

        if (isset($data['user_name'])) {
            $users->where('user_name', 'like', '%' . $data['user_name'] . '%');
        }

        $users = $users->orderBy('user_account', 'asc')->get();

        return $users;
    }

    public function getUserById($id)
    {
        $authUser = Auth::user();

        $user = User::with(['roles', 'supplier'])
            ->where('agent_id', $authUser->agent_id);

        $user = $user->find($id);

        return $user;
    }

    public function addUser($inputData)
    {
        $authUser = Auth::user();
        $result = false;

        DB::beginTransaction();
        try {
            $newUser = User::create([
                'agent_id' => $authUser->agent_id,
                'user_account' => $inputData['user_account'],
                'user_name' => $inputData['user_name'],
                'active' => $inputData['active'],
                'user_password' => md5($inputData['user_password']),
                'user_email' => $inputData['user_email'],
                'supplier_id' => $inputData['supplier_id'] ?? null,
                'created_by' => $authUser->id,
                'updated_by' => $authUser->id,
            ]);

            $roles = $inputData['roles'] ?? [];

            $newUser->roles()->syncWithPivotValues($roles, [
                'created_by' => $authUser->id,
                'updated_by' => $authUser->id,
            ]);

            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
        }

        return $result;
    }

    public function updateUser($inputData)
    {
        $authUser = Auth::user();
        $result = false;

        DB::beginTransaction();
        try {
            $userData = [
                'user_name' => $inputData['user_name'],
                'active' => $inputData['active'],
                'user_email' => $inputData['user_email'],
                'supplier_id' => $inputData['supplier_id'] ?? null,
                'updated_by' => $authUser->id,
            ];

            if (isset($inputData['user_password'])) {
                $userData['user_password'] = md5($inputData['user_password']);
            }

            $user = User::findOrFail($inputData['id']);
            $user->update($userData);

            $roles = $inputData['roles'] ?? [];

            $user->roles()->syncWithPivotValues($roles, [
                'created_by' => $authUser->id,
                'updated_by' => $authUser->id,
            ]);

            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return $result;
    }

    public function updateProfile($inputData)
    {
        $authUser = Auth::user();
        $result = false;

        $userData = [
            'user_name' => $inputData['user_name'],
            'user_email' => $inputData['user_email'],
            'updated_by' => $authUser->id,
        ];

        if (isset($inputData['pwd'])) {
            $userData['user_password'] = md5($inputData['pwd']);
        }

        try {
            User::findOrFail($authUser->id)->update($userData);
            $result = true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
     * 設定導覽列session
     *
     * @return void
     */
    public function setMenuSession()
    {
        $user = auth()->user();
        $authorizedPermissionDetailIds = collect();

        // 取得使用者
        $user = User::with([
            'roles.permissionDetails' => function ($query) {
                return $query->where('role_permission_details.auth_query', 1);
            },
        ])->find($user->id);

        // 取得使用者被授權的子權限
        if ($user->roles->isNotEmpty()) {
            $user->roles->each(function ($role) use (&$authorizedPermissionDetailIds) {
                $authorizedPermissionDetailIds = $authorizedPermissionDetailIds->merge($role->permissionDetails->pluck('id'));
            });

            $authorizedPermissionDetailIds = $authorizedPermissionDetailIds->unique();
        }

        // 取得權限資料
        $permissions = Permission::with([
            'permissionDetails' => function ($query) {
                return $query->orderBy('sort', 'asc');
            },
        ])
            ->where('type', 'menu')
            ->orderBy('sort', 'asc')
            ->get();

        // 取得所有路由名稱
        $routes = Route::getRoutes();
        $routeNames = collect();
        foreach ($routes as $route) {
            $routeName = $route->getName();

            if (isset($routeName)) {
                $routeNames->push($routeName);
            }
        }

        $menu = [];
        $permissions->each(function ($permission) use (&$menu, $authorizedPermissionDetailIds, $routeNames) {
            $subMenu = [];

            if ($permission->permissionDetails->isNotEmpty()) {
                $permission->permissionDetails->each(function ($permissionDetail) use (&$subMenu, $authorizedPermissionDetailIds, $routeNames) {
                    if ($authorizedPermissionDetailIds->contains($permissionDetail->id)
                        && $routeNames->contains($permissionDetail->code)) {
                        $subMenu[] = [
                            'icon' => $permissionDetail->icon,
                            'name' => $permissionDetail->name,
                            'code' => $permissionDetail->code,
                        ];
                    }
                });
            }

            if (!empty($subMenu)) {
                $menu[] = [
                    'icon' => $permission->icon,
                    'name' => $permission->name,
                    'sub_menu' => $subMenu,
                ];
            }
        });

        session(['dradvice_menu' => $menu]);
    }
}
