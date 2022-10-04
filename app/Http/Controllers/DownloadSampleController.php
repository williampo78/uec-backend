<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class DownloadSampleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($fileName,Request $request)
    {
        $rename = $request->input('rename') ;
        $type = $request->input('type');
        try {
            $filePath = public_path('sample/'.$fileName);
            if(!empty($rename) && !empty($type)){
                return response()->download($filePath,"{$rename}.{$type}");
            }else{
                return response()->download($filePath);

            }
        } catch (\Exception $e) {

            return view('backend.error',['message'=>'該頁面不存在']);
        }

    }

}
