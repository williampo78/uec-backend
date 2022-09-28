<?php

namespace App\Http\Controllers;

class DownloadSampleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($fileName)
    {
        try {
            $filePath = public_path('sample/' . $fileName);

            return response()->download($filePath);
        } catch (\Exception $e) {

            return view('backend.error', ['message' => '該頁面不存在']);
        }

    }
}
