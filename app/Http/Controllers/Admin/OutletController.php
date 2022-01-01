<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Validation\BankChargesValidation;
use App\Http\Validation\CreateOutletValidation;
use App\Http\Validation\OutletValidation;
use App\Http\Validation\UpdateOutletValidation;
use App\Models\Outlet;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function store(CreateOutletValidation $request)
    {

        $outlet = new Outlet();
        $outlet->user_id              = Auth::user()->_id;
        $outlet->outlet_no            = rand(0000, 9999);
        $outlet->outlet_type          = $request->outlet_type;
        $outlet->user_type            = $request->user_type;
        $outlet->mobile_no            = $request->mobile_no;
        $outlet->alternate_number     = $request->alternate_number;
        $outlet->retailer_name        = $request->retailer_name;
        $outlet->email                = $request->email;
        $outlet->gender               = $request->gender;
        $outlet->permanent_address    = $request->permanent_address;
        $outlet->outlet_name          = $request->outlet_name;
        $outlet->outlet_address       = $request->outlet_address;
        $outlet->state                = $request->state;
        $outlet->city                 = $request->city;
        $outlet->pincode              = $request->pincode;
        $outlet->incorporation_date   = $request->incorporation_date;
        $outlet->date_of_birth        = $request->date_of_birth;
        $outlet->id_proff             = $request->id_proff;
        $outlet->address_proff        = $request->address_proff;
        $outlet->pancard              = $request->pancard;
        $outlet->outlet_gst_number    = $request->outlet_gst_number;
        $outlet->money_transfer_otion = $request->money_transfer_otion;
        $outlet->status               = $request->status;
        $outlet->account_status       = $request->account_status;

        //for office photo
        if (!empty($request->file('office_photo')))
            $outlet->office_photo  = singleFile($request->file('office_photo'), 'attachment');

        //for office address proff
        if (!empty($request->file('office_address_proff')))
            $outlet->office_address_proff  = singleFile($request->file('office_address_proff'), 'attachment');

        //for user photo
        if (!empty($request->file('profile_image')))
            $outlet->profile_image  = singleFile($request->file('profile_image'), 'attachment');

        //for upload id
        if (!empty($request->file('upload_id')))
            $outlet->upload_id  = singleFile($request->file('upload_id'), 'attachment');

        //for upload address
        if (!empty($request->file('upload_address')))
            $outlet->upload_address  = singleFile($request->file('upload_address'), 'attachment');

        if ($outlet->save()){
            $this->createUser($outlet->_id, $request);
            return response(['status' => 'success', 'msg' => 'Outlet Created Successfully!']);
        }

        return response(['status' => 'error', 'msg' => 'Outlet Not Created!']);
    }



    private function createUser($outlet_id, $request)
    {
        $user = new User();
        $user->full_name = $request->retailer_name;
        $user->email = $request->email;
        $user->mobile_number = $request->mobile_no;
        $user->password = Hash::make($request->password);
        $user->role  = 'retailer';
        $user->outlet_id = $outlet_id;
        $user->verify_otp = 0;
        $user->save();
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


    public function update(UpdateOutletValidation $request, $id)
    {

        $outlet = Outlet::find($id);

        $count = $outlet->where('mobile_no', $request->mobile_no)->where('_id', '!=', $id)->count();
        if ($count > 0)
            return response(json_encode(array('validation' => ['mobile_no' => 'This Mobile Number used by Other.'])));

        $count = $outlet->where('mobile_no', $request->mobile_no)->where('_id', '!=', $id)->count();
        if ($count > 0)
            return response(json_encode(array('validation' => ['mobile_no' => 'This Mobile Number used by Other.'])));

        $outlet->outlet_type          = $request->outlet_type;
        $outlet->user_type            = $request->user_type;
        $outlet->mobile_no            = $request->mobile_no;
        $outlet->alternate_number     = $request->alternate_number;
        $outlet->retailer_name        = $request->retailer_name;
        $outlet->email                = $request->email;
        $outlet->gender               = $request->gender;
        $outlet->permanent_address    = $request->permanent_address;
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
        $outlet->outlet_gst_number    = $request->outlet_gst_number;
        $outlet->money_transfer_otion = $request->money_transfer_otion;
        $outlet->status               = $request->status;
        $outlet->account_status       = $request->account_status;

        //for office photo
        if (!empty($request->file('office_photo')))
            $outlet->office_photo  = singleFile($request->file('office_photo'), 'attachment');

        //for office address proff
        if (!empty($request->file('office_address_proff')))
            $outlet->office_address_proff  = singleFile($request->file('office_address_proff'), 'attachment');

        //for user photo
        if (!empty($request->file('profile_image')))
            $outlet->profile_image  = singleFile($request->file('profile_image'), 'attachment');

        //for upload id
        if (!empty($request->file('upload_id')))
            $outlet->upload_id  = singleFile($request->file('upload_id'), 'attachment');

        //for upload address
        if (!empty($request->file('upload_address')))
            $outlet->upload_address  = singleFile($request->file('upload_address'), 'attachment');

        if ($outlet->save())
            return response(['status' => 'success', 'msg' => 'Outlet Updated Successfully!']);

        return response(['status' => 'error', 'msg' => 'Outlet not Updated!']);
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



    public function outletBankCharges($id)
    {

        try {
            $outlet = Outlet::find($id);
            if (!empty($outlet)) {
                $data['bank_charges'] = $outlet->bank_charges;
                $data['id'] = $outlet->_id;
                return view('admin.outlet.bank_charges', $data);
            }
            return redirect('500');
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function outletAddBankCharges(BankChargesValidation $request)
    {
        try {

            $outlet_id = $request->id;
            $outlet = Outlet::find($outlet_id);

            $bank_changes = $outlet->bank_charges;
            foreach ($bank_changes as $charge) {
                if ($charge['from_amount'] == $request->from_amount && $charge['to_amount'] == $request->to_amount)
                    return response(['status' => 'error', 'msg' => 'This Amount Slab is already Added.']);
            }

            $bank_charges_val = array();
            if (!empty($outlet->bank_charges) && is_array($outlet->bank_charges))
                $bank_charges_val = $outlet->bank_charges;

            $bank_charges_val[] = [
                'from_amount' => $request->from_amount,
                'to_amount'   => $request->to_amount,
                'type'        => $request->type,
                'charges'     => $request->charges,
                'status'      => 1
            ];

            $outlet->bank_charges = $bank_charges_val;
            if ($outlet->save())
                return response(['status' => 'success', 'msg' => 'Bank Charges Added Successfully!']);

            return response(['status' => 'error', 'msg' => 'Bank Charges not Added Successfully!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function outletEditBankCharges(Request $request, $id)
    {
        try {
            $outlet = Outlet::select('bank_charges')->find($id);
            $key = $request->key;
            $bank_charges = $outlet->bank_charges[$key];

            return response(['status' => 'success', 'data' => $bank_charges]);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function outletUpdateBankCharges(BankChargesValidation $request)
    {
        try {
            $key = $request->key;

            $id = $request->id;
            $outlet = Outlet::find($id);

            $bank_changes = $outlet->bank_charges;

            foreach ($bank_changes as $nkey => $charge) {
                if ($charge['from_amount'] == $request->from_amount && $charge['to_amount'] == $request->to_amount && $key != $nkey)
                    return response(['status' => 'error', 'msg' => 'This Amount Slab is already Exist.']);
            }

            $bank_charge = array();
            if (!empty($outlet->bank_charges) && is_array($outlet->bank_charges))
                $bank_charge = $outlet->bank_charges;

            //check name and value is not empry
            $bank_charge[$key]['from_amount'] = $request->from_amount;
            $bank_charge[$key]['to_amount']  = $request->to_amount;
            $bank_charge[$key]['type']       = $request->type;
            $bank_charge[$key]['charges']    = $request->charges;

            $outlet->bank_charges          = $bank_charge;

            if ($outlet->save())
                return response(['status' => 'success', 'msg' => 'Field Updated successfully!']);


            return response(['status' => 'error', 'msg' => 'Field not Updated Field!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function bankChargesStatus($id, $key, $status)
    {
        try {
            $outlet = Outlet::find($id);

            $bank_charge = array();
            if (!empty($outlet->bank_charges) && is_array($outlet->bank_charges))
                $bank_charge = $outlet->bank_charges;

            $bank_charge[$key]['status']   = (int)$status;

            $outlet->bank_charges          = $bank_charge;

            $outlet->save();

            if ($bank_charge[$key]['status'] == 1)
                return response(['status' => 'success', 'msg' => 'Bank Charges is Active!', 'val' => $bank_charge[$key]['status']]);

            return response(['status' => 'success', 'msg' => 'Bank Charges is Inactive!', 'val' => $bank_charge[$key]['status']]);
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
            // $action = '<a href="javascript:void(0);" class="text-orange banckModal"  data-toggle="tooltip" data-placement="bottom" title="Bank Charges" outlet_id="' . $val->_id . '"><i class="fas fa-piggy-bank"></i></a>&nbsp;&nbsp;';
            $action = '<a href="' . url('admin/outlet-bank-charges/' . $val->_id) . '" class="text-orange"  data-toggle="tooltip" data-placement="bottom" title="Bank Charges"><i class="fas fa-piggy-bank"></i></a>&nbsp;&nbsp;';
            $action .= '<a href="' . url('admin/outlets/' . $val->_id . '/edit') . '" class="text-info" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="far fa-edit"></i></a>';

            if ($val->account_status == 1) {
                $status = ' <a href="javascript:void(0);"><span class="badge badge-success activeVer" id="active_' . $val->_id . '" _id="' . $val->_id . '" val="0">Active</span></a>';
            } else {
                $status = ' <a href="javascript:void(0)"><span class="badge badge-danger activeVer" id="active_' . $val->_id . '" _id="' . $val->_id . '" val="1">Inactive</span></a>';
            }

            $amount = User::select('available_amount')->where('outlet_id', $val->_id)->first();

            $available_amount = 0;
            if (!empty($amount))
            $available_amount = $amount->available_amount;

            $dataArr[] = [
                'sl_no'             => $i,
                'outlet_no'         => $val->outlet_no,
                'name'              => $val->retailer_name,
                'mobile_no'         => $val->mobile_no,
                'outlet_name'       => $val->outlet_name,
                'state'             => $val->state,
                'available_blance'  => (!empty($available_amount)) ? mSign($available_amount) : mSign(0),

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



    //for export sample import csv file
    public function sampleCsv()
    {
        try {
            //file name here
            $file_name = 'sample_order_csv';

            $fields = ['Group Name', 'Agent Code', 'Status(Active/Inactive)'];

            $delimiter = ",";
            if(!file_exists('sampleCsv'))
             mkdir('sampleCsv', 0777, true);

            $f = fopen('sampleCsv/' . $file_name . '.csv', 'w');
            fputcsv($f, $fields, $delimiter);

            // Move back to beginning of file
            fseek($f, 0);

            // headers to download file
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $file_name . '.csv";');
            readfile('sampleCsv/' . $file_name . '.csv');
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


}
