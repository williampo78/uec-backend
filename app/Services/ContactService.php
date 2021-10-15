<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactService
{
    public function __construct()
    {
    }
    public function getContact($table_name, $table_id)
    {
        return Contact::where('table_name', $table_name)->where('table_id', $table_id)->get();
    }
    public function editContact()
    {

    }
    public function delContact($table_name, $id)
    {
        Contact::where('table_name', $table_name)->where('id', $id)->delete();
    }
    public function createContact($table_name, $data)
    {
        $contact = json_decode($data, true);
        $createData = $update = [];
        // dump($contact) ;
        foreach ($contact as $key => $val) {
            unset($val['created_at']);
            unset($val['updated_at']);
            if ($val['id'] == '') {
                unset($val['id']);
                array_push($createData, $val);
            } else {
                unset($val['id']);
                array_push($update, $val);
            }
        }
        if (count($createData) !== 0) {
            try {
                Contact::insert($createData);
            } catch (\Exception $e) {
                Log::info($e);
            }
        }
        // dd($update) ;
        if (count($update) !== 0) {
            try {
                foreach ($update as $val) {
                    DB::table('contact')->where('table_name', $table_name)->where('table_id', $val['table_id'])->update($val);
                }
            } catch (\Exception $e) {
                Log::info($e);
            }
        }
        return true;
    }
}
