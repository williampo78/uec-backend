<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        return response()->json(['status'=>true,'reuslt'=>'Text OK']);
    }
}
