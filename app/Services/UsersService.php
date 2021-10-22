<?php


namespace App\Services;
use App\Models\Users;
use App\Models\UserRoles;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UsersService
{

    public function getUsers($data)
    {
        $agent_id = Auth::user()->agent_id;
        $users = Users::where('agent_id', $agent_id);

        if (isset($data['active'])) {
            $users->where('active', $data['active']);
        }

        if (isset($data['user_account'])) {
            $users->where('user_account', 'like', '%' . $data['user_account'] . '%');
        }

        if (isset($data['user_name'])) {
            $users->where('user_name', 'like', '%' . $data['user_name'] . '%');
        }

        $users = $users->orderBy('user_account', 'ASC')->get();

        return $users;
    }

    public function addUser($inputdata, $act)
    {
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        DB::beginTransaction();
        try {
            $userData = [];
            $userData['agent_id'] = Auth::user()->agent_id;
            $userData['user_account'] = $inputdata['user_account'];
            $userData['user_password'] = md5($inputdata['user_password']);
            $userData['user_name'] = $inputdata['user_name'];
            $userData['user_email'] = $inputdata['user_email'];
            $userData['active'] = $inputdata['active'];
            $userData['supplier_id'] = isset($inputdata['supplier_id'])?$inputdata['supplier_id']:'';
            $userData['created_by'] = $user_id;
            $userData['created_at'] = $now;
            $userData['updated_by'] = $user_id;
            $userData['updated_at'] = $now;
            if ($act == 'add') {
                $new_id = Users::insertGetId($userData);
            } else if ($act =='upd') {
                Users::where('id' , $inputdata['id'])->update($userData);
                $new_id = $inputdata['id'];
            }

            $detailData = [];
            //不管新增或編輯先把原有的角色都刪除
            UserRoles::where('user_id', '=', $new_id)->delete();
            foreach ($inputdata['role'] as $k1 => $v1) {    //有勾選才會寫入細項權限
                $detailData['role_id'] = $v1;
                $detailData['user_id'] = $new_id;
                $detailData['created_by'] = $user_id;
                $detailData['created_at'] = $now;
                $detailData['updated_by'] = -1;
                $detailData['updated_at'] = $now;
                UserRoles::insert($detailData);
            }
            DB::commit();
            $result = true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = false;
        }
        return $result;
    }
}
