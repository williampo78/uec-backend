<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SupplierService
{
    public function __construct()
    {
    }

    public function getSuppliers($query_data = [])
    {
        $agent_id = Auth::user()->agent_id;
        $result = [];

        $result = Supplier::where('agent_id', $agent_id);

        if (isset($query_data['active'])) {
            if ($query_data['active'] == 1) {
                $result = $result->where('active', 1);
            } else {
                $result = $result->where('active', 0);
            }
        }

        $result = $result->get();

        return $result;
    }

    public function addSupplier($inputdata)
    {
        $inputdata['agent_id'] = Auth::user()->agent_id;
        $inputdata['created_by'] = Auth::user()->id;
        $inputdata['updated_by'] = Auth::user()->id;
        DB::beginTransaction();
        try {
            DB::commit();
            Supplier::create($inputdata);
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $result = false;
        }
        return $result ;

    }
    public function checkDisplayNumber($DisplayNumber){
        $check = Supplier::where('display_number',$DisplayNumber)->count();
        return $check == 0 ; 
    }

    public function showSupplier($id)
    {
        return Supplier::where('id', $id)->get()->first();
    }

    public function updateSupplier($input,$id)
    {
        unset($input['_token']);
        unset($input['_method']);

        return Supplier::where('id', $id)->update($input);
    }
    public function getPaymentTerms(){
       return DB::table('lookup_values')->where('lookup_type_id','9')->get();
    }


}
