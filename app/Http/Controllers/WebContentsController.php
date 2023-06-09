<?php

namespace App\Http\Controllers;

use App\Models\WebContent;
use App\Services\UniversalService;
use App\Services\WebContentsService;
use Illuminate\Http\Request;

class WebContentsController extends Controller
{
    private $webContentsService;
    private $universalService;

    public function __construct(
        WebContentsService $webContentsService,
        UniversalService $universalService
    ) {
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
        $data['category'] = $this->webContentsService->getCategory('FOOTER_CATEGORY');
        $data['footer'] = ($getData ? $this->webContentsService->getFooter($getData, 'FOOTER') : []);
        $data['user'] = $this->universalService->getUser();
        $data['code'] = $this->universalService->getLookupValues('FOOTER_CATEGORY');
        $data['target'] = $this->universalService->getFooterContentTarget();
        $data['getData'] = $getData;
        return view('backend.web_contents.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['category'] = $this->webContentsService->getCategory('FOOTER_CATEGORY');
        $data['target'] = $this->universalService->getFooterContentTarget();
        return view('backend.web_contents.add', compact('data'));
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
        $route_name = 'webcontents';
        $input['apply_to'] = 'FOOTER';
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
        $data['category'] = $this->webContentsService->getCategory('FOOTER_CATEGORY');
        $data['target'] = $this->universalService->getFooterContentTarget();
        $data['webcontent'] = WebContent::find($id);
        return view('backend.web_contents.view', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['category'] = $this->webContentsService->getCategory('FOOTER_CATEGORY');
        $data['target'] = $this->universalService->getFooterContentTarget();
        $data['webcontent'] = WebContent::find($id);
        return view('backend.web_contents.upd', compact('data'));
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
        $input = $request->except('_token', '_method');
        $act = 'upd';
        $route_name = 'webcontents';
        $input['id'] = $id;
        $input['apply_to'] = 'FOOTER';
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
