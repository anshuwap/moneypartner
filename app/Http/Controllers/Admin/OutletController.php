<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Validation\OutletValidation;
use App\Models\Outlet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutletController extends Controller
{

    public function index()
    {
        try {

            return view('admin.outlet.list');
        } catch (Exception $e) {
            return redirect('500');
        }
    }

    public function create()
    {
        try {
            return view('admin.outlet.create');
        } catch (Exception $e) {
            return redirect('500');
        }
    }

    public function store(OutletValidation $request)
    {

        $outlet = new Outlet();
        $outlet->user_id              = Auth::user()->_id;
        $outlet->outlet_type          = $request->outlet_type;
        $outlet->user_type            = $request->user_type;
        $outlet->mobile_no            = $request->mobile_no;
        $outlet->alternate_number     = $request->alternate_number;
        $outlet->retailer_name        = $request->retailer_name;
        $outlet->email                = $request->email;
        $outlet->gender               = $request->gender;
        $outlet->permanent_address    = $request->permanent_address;
        $outlet->outlet_outlet_type   = $request->outlet_outlet_type;
        $outlet->outlet_name          = $request->outlet_name;
        $outlet->outlet_address       = $request->outlet_address;
        $outlet->state                = $request->state;
        $outlet->city                 = $request->city;
        $outlet->pincode              = $request->pincode;
        $outlet->incorporation_date   = $request->incorporation_date;
        $outlet->company_pancard      = $request->company_pancard;
        $outlet->date_of_birth        = $request->date_of_birth;
        $outlet->id_proff             = $request->id_proff;
        $outlet->address_proff        = $request->address_proff;
        $outlet->pancard              = $request->pancard;
        $outlet->gst_number           = $request->gst_number;
        $outlet->outlet_gst_number    = $request->outlet_gst_number;
        $outlet->money_transfer_otion = $request->money_transfer_otion;
        $outlet->payout_option        = $request->payout_option;
        $outlet->status               = $request->status;
        $outlet->account_status       = $request->account_status;

        //for office photo
        if (!empty($request->file('office_photo')))
            $outlet->office_photo  = singleFile($request->file('office_photo'), 'attachment');

        //for office address proff
        if (!empty($request->file('office_address_proff')))
            $outlet->office_address_proff  = singleFile($request->file('office_address_proff'), 'attachment');

        //for user photo
        if (!empty($request->file('user_photo')))
            $outlet->user_photo  = singleFile($request->file('user_photo'), 'attachment');

        //for upload id
        if (!empty($request->file('upload_id')))
            $outlet->upload_id  = singleFile($request->file('upload_id'), 'attachment');

        //for upload address
        if (!empty($request->file('upload_address')))
            $outlet->upload_address  = singleFile($request->file('upload_address'), 'attachment');

        if ($outlet->save())
            return response(['status' => 'success', 'msg' => 'Outlet Created Successfully!']);
    }

    public function show(outlet $outlet)
    {
    }


    public function edit(outlet $outlet)
    {

        try {
            return view('admin.outlet.edit', compact('outlet'));
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function update(Request $request, $id)
    {

        $outlet = Outlet::find($id);

        $outlet->outlet_type          = $request->outlet_type;
        $outlet->user_type            = $request->user_type;
        $outlet->mobile_no            = $request->mobile_no;
        $outlet->alternate_number     = $request->alternate_number;
        $outlet->retailer_name        = $request->retailer_name;
        $outlet->email                = $request->email;
        $outlet->gender               = $request->gender;
        $outlet->permanent_address    = $request->permanent_address;
        $outlet->outlet_outlet_type   = $request->outlet_outlet_type;
        $outlet->outlet_name          = $request->outlet_name;
        $outlet->outlet_address       = $request->outlet_address;
        $outlet->state                = $request->state;
        $outlet->city                 = $request->city;
        $outlet->pincode              = $request->pincode;
        $outlet->incorporation_date   = $request->incorporation_date;
        $outlet->company_pancard      = $request->company_pancard;
        $outlet->date_of_birth        = $request->date_of_birth;
        $outlet->id_proff             = $request->id_proff;
        $outlet->address_proff        = $request->address_proff;
        $outlet->pancard              = $request->pancard;
        $outlet->gst_number           = $request->gst_number;
        $outlet->outlet_gst_number    = $request->outlet_gst_number;
        $outlet->money_transfer_otion = $request->money_transfer_otion;
        $outlet->payout_option        = $request->payout_option;
        $outlet->status               = $request->status;
        $outlet->account_status       = $request->account_status;

        //for office photo
        if (!empty($request->file('office_photo')))
            $outlet->office_photo  = singleFile($request->file('office_photo'), 'attachment');

        //for office address proff
        if (!empty($request->file('office_address_proff')))
            $outlet->office_address_proff  = singleFile($request->file('office_address_proff'), 'attachment');

        //for user photo
        if (!empty($request->file('user_photo')))
            $outlet->user_photo  = singleFile($request->file('user_photo'), 'attachment');

        //for upload id
        if (!empty($request->file('upload_id')))
            $outlet->upload_id  = singleFile($request->file('upload_id'), 'attachment');

        //for upload address
        if (!empty($request->file('upload_address')))
            $outlet->upload_address  = singleFile($request->file('upload_address'), 'attachment');

        if ($outlet->save())
            return response(['status' => 'success', 'msg' => 'Outlet Updated Successfully!']);
    }


    public function destroy(outlet $outlet)
    {
        //
    }


    public function outletStatus(Request $request)
    {

        try {
            $outlet = Outlet::find($request->id);
            $outlet->account_status = (int)$request->status;
            $outlet->save();
            if ($outlet->account_status == 1)
                return response(['status' => 'success', 'msg' => 'This Account is Active!', 'val' => $outlet->account_status]);

            return response(['status' => 'success', 'msg' => 'This Account is Inactive!', 'val' => $outlet->account_status]);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }



    public function ajaxList(Request $request)
    {

        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        $search_arr = $request->search;
        $searchValue = $search_arr['value'];

        // count all data
        $totalRecords = Outlet::AllCount();

        //$selectColoum = "p.id as id,p.unit,p.thumbnail as thumbnail,p.name as name,p.description as description,p.status as status,p.discount_price as discount_price,c.name as category";

        if (!empty($searchValue)) {
            // count all data
            $totalRecordswithFilter = Outlet::LikeColumn($searchValue);
            $data = Outlet::GetResult($searchValue);
        } else {
            // get per page data
            $totalRecordswithFilter = $totalRecords;
            $data = Outlet::offset($start)->limit($length)->orderBy('created', 'DESC')->get();
        }
        $dataArr = [];
        $i = 1;

        foreach ($data as $val) {
            $action = '<a href="' . url('admin/outlets/' . $val->_id . '/edit') . '" class="text-info"><i class="far fa-edit"></i></a>&nbsp;&nbsp;';
            // $action .= '<a href="javascript:void(0);" class="text-danger delete"  onclick=deleteRecord(' . $val->id . ')><i class="far fa-trash-alt"></i></a>';

            if ($val->account_status == 1) {
                $status = ' <a href="javascript:void(0);"><span class="badge badge-success activeVer" id="active_'.$val->_id.'" _id="'.$val->_id.'" val="0">Active</span></a>';
            } else {
                $status = ' <a href="javascript:void(0)"><span class="badge badge-danger activeVer" id="active_'.$val->_id.'" _id="'.$val->_id.'" val="1">Inactive</span></a>';
            }

            $dataArr[] = [
                'sl_no'             => $i,
                'outlet_no'         => '0091',
                'name'              => $val->retailer_name,
                'mobile_no'         => $val->mobile_no,
                'outlet_name'       => $val->outlet_name,
                'state'             => $val->state,
                'available_blance'  => '',

                'created_date'      => date('Y-m-d', $val->created),
                'status'            => $status,
                'action'            => $action
            ];
            $i++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" =>  $totalRecordswithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $dataArr
        );
        echo json_encode($response);
        exit;
    }
}
