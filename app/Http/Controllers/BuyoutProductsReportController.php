<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\BuyoutProductsReportExport;
use Maatwebsite\Excel\Facades\Excel;

class BuyoutProductsReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $export = new BuyoutProductsReportExport([
            [1, 2, 3],
            [4, 5, 6]
        ]);
        
        return Excel::download($export, 'invoices.xlsx');
        // return Excel::download(new BuyoutProductsReportExport([
        //     [1, 2, 3],
        //     [4, 5, 6]
        // ]), 'users.xlsx');
        echo 'hello~~';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function export()
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }
}
