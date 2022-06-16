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

    public function addDepartment($data)
    {
        $user = auth()->user();

        try {
            Department::create([
                'agent_id' => $user->agent_id,
                'number' => $data['number'],
                'name' => $data['name'],
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function showDepartment($id)
    {
        return Department::find($id);
    }

    public function updateDepartment($id, $data)
    {
        $user = auth()->user();

        try {
            Department::findOrFail($id)->update([
                'number' => $data['number'],
                'name' => $data['name'],
                'updated_by' => $user->id,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
