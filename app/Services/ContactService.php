<?php

namespace App\Services;

use App\Models\Contact;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class ContactService
{
    public function __construct()
    {
    }
    public function getContact($table_name,$table_id){
        return Contact::where('table_name',$table_name)->where('table_id',$table_id)->get();
    }
    public function editContact(){

    }
    public function delContact($table_name , $id){
        Contact::where('table_name',$table_name)->where('id',$id)->delete();
    }
    public function createContact($table_name , $data){
        $Contact = json_decode($data, true);
        dump($Contact) ; 
        dd($table_name , $data) ; 


    }
}
