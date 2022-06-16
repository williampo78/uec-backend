<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Warehouse::all();
        return view('backend.warehouse.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.warehouse.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only(['number', 'name']);
        $user = auth()->user();

        try {
            $createdWarehouse = Warehouse::create([
                'agent_id' => $user->agent_id,
                'number' => $data['number'],
                'name' => $data['name'],
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        $route_name = 'warehouse';
        $act = 'add';
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

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Warehouse::find($id);

        return view('backend.warehouse.upd', compact('data'));
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
        $data = $request->only(['number', 'name']);
        $user = auth()->user();

        try {
            Warehouse::findOrFail($id)->update([
                'number' => $data['number'],
                'name' => $data['name'],
                'updated_by' => $user->id,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        $route_name = 'warehouse';
        $act = 'upd';
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
