<?php


namespace App\Services;

use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class DepartmentService
{

    public function getDepartment()
    {
        $agent_id = Auth::user()->agent_id;
        return Department::where('agent_id', $agent_id)->get();
    }

    public function addDepartment($inputdata)
    {
        try {
            return Department::insert($inputdata);
        } catch (\Exception $e) {
            Log::info($e);
        }
    }

    public function showDepartment($id)
    {
        return Department::where('id', $id)->get()->first();
    }


    public function updateDepartment($input,$id)
    {
        return Department::where('id', $id)->update($input);
    }
}
