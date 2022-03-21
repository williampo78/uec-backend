<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserRoles;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
}
