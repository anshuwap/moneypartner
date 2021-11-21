<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $outlet = New Outlet();
        $outlet->user_id = Auth::user()->_id;
        $outlet->outlet_type = $request->outlet_type;
        $outlet->user_type = $request->user_type;
        $outlet->mobile_no = $request->mobile_no;
        $outlet->alternate_number = $request->alternate_number;
        $outlet->retailer_name = $request->retailer_name;
        $outlet->email = $request->email;
        $outlet->gender = $request->gender;
        $outlet->permanent_address = $request->permanent_address;
        $outlet->outlet_outlet_type = $request->outlet_outlet_type;
        $outlet->outlet_name = $request->outlet_name;
        $outlet->outlet_address = $request->outlet_address;
        $outlet->state = $request->state;
        $outlet->city = $request->city;
        $outlet->pincode = $request->pincode;
        $outlet->incorporation_date = $request->incorporation_date;
        $outlet->company_pancard = $request->company_pancard;
        $outlet->date_of_birth = $request->date_of_birth;
        $outlet->id_proff = $request->id_proff;
        $outlet->address_proff = $request->address_proff;
        $outlet->pancard = $request->pancard;
        $outlet->money_transfer_otion = $request->money_transfer_otion;
        $outlet->payout_option = $request->payout_option;

        if($outlet->save())
        return response(['status' => 'success', 'msg' => 'Outlet Created successfully!']);
    }

    public function show(outlet $outlet)
    {

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
