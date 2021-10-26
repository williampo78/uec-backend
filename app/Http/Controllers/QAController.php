<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\WebContentsService;
use App\Services\UniversalService;
use App\Models\WebContents;
class QAController extends Controller
{
    private $webContentsService;

    public function __construct(WebContentsService $webContentsService, UniversalService $universalService)
    {
        $this->webContentsService = $webContentsService;
        $this->universalService = $universalService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $getData = $request->all();
        $data['category'] = $this->webContentsService->getCategory('QA_CATEGORY');
        $data['footer'] = ($getData ? $this->webContentsService->getFooter($getData, 'QA') : []);
        $data['user'] = $this->universalService->getUser();
        $data['code'] = $this->universalService->getQACategory();
        $data['getData'] = $getData;
        return view('Backend.QA.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['category'] = $this->webContentsService->getCategory('QA_CATEGORY');
        return view('Backend.QA.add', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->input();
        unset($input['_token']);
        $act = 'add';
        $route_name = 'qa';
        $input['apply_to'] = 'QA';
        $this->webContentsService->addWebContent($input, $act);
        return view('backend.success', compact('route_name', 'act'));
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
        $data['category'] = $this->webContentsService->getCategory('QA_CATEGORY');
        $data['webcontent'] = WebContents::find($id);
        return view('Backend.QA.upd', compact('data'));
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
        $input = $request->input();
        $input = $request->except('_token' , '_method');
        $act = 'upd';
        $route_name = 'qa';
        $input['id'] = $id;
        $input['apply_to'] = 'QA';
        $result = $this->webContentsService->addWebContent($input, $act);
        return view('backend.success', compact('route_name', 'act'));
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
}
