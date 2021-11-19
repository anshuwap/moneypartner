<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use Exception;
use Illuminate\Http\Request;

class OutletController extends Controller
{

    public function index()
    {
    try{

        return view('admin.outlet.list');
    }catch(Exception $e){
    return redirect('500');
    }
    }

    public function create()
    {
        try{
            return view('admin.outlet.create');
        }catch(Exception $e){
        return redirect('500');
        }
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


    public function show(outlet $outlet)
    {
        //
    }


    public function edit(outlet $outlet)
    {
        try{
            return view('admin.edit');
        }catch(Exception $e){
        return redirect('500');
        }
    }


    public function update(Request $request, outlet $outlet)
    {
        //
    }


    public function destroy(outlet $outlet)
    {
        //
    }
}
