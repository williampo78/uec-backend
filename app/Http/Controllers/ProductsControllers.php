<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductsService;
use Storage ;
class ProductsControllers extends Controller
{
    private $productsService;

    public function __construct(ProductsService $productsService)
    {
        $this->productsService = $productsService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $in = $request->input() ; 
        $result = $this->productsService->get_Products($request) ; 

        return view('Backend.Products.list',$result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Backend.Products.input');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function testview(){        
        return view('Backend.Products.test');
    }
    public function upload_img(Request $request){
        if($request->hasFile('photo')){//判斷照片
            $s3 = Storage::disk('s3');
            $photo = $request->file('photo') ;
            if($s3->put('/photo',$photo)){
                return 'success';
            }
            return "S3 faild";
        }
        // dd($request);
        // if($request->hasFile('photo')){
        //     $s3 = Storage::disk('s3');
        //     $photo = $request->file('photo');

        //     if($s3->put('/photo',$photo)){
        //         return 'success';
        //     }
        //     return "S3 faild";
        // }
        // return "no file";
    }

}
