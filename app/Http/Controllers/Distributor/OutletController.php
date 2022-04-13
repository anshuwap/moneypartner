<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Http\Validation\BankChargesValidation;
use App\Http\Validation\CreateOutletValidation;
use App\Http\Validation\DistributorOutletCreateValidation;
use App\Http\Validation\DistributorOutletUpdateValidation;
use App\Http\Validation\UpdateOutletValidation;
use App\Models\Outlet;
use App\Models\User;
use App\Support\Email;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OutletController extends Controller
{

    public function index()
    {
        try {

            return view('distributor.outlet.list');
        } catch (Exception $e) {
            return redirect('500');
        }
    }

    public function create()
    {
        try {
            return view('distributor.outlet.create');
        } catch (Exception $e) {
            return redirect('500');
        }
    }

    public function store(DistributorOutletCreateValidation $request)
    {

        $pin = rand(1111, 9999);
        $outlet = new Outlet();
        $outlet->user_id              = Auth::user()->_id;
        $outlet->created_by           = 'distributor';
        $outlet->outlet_no            = rand(1111, 9999);
        $outlet->outlet_type          = 'retailer';
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
        $outlet->status               = $request->status;
        $outlet->account_status       = (int)$request->account_status;
        $outlet->pin                  = $pin;

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

        if ($outlet->save()) {
            $this->createUser($outlet->_id, $request, $pin);

            $msg = '<td>
         <h5 class="text-center">Welcome in Moneypartner Panel<.</h3
            <h3 class="otp"> <p>Your PIN is-' . rand('1111', 9999) . '</p>
            <p>Your User Id is-' . $request->email . '</p>
            <p>Your Password is-' . $request->password . '</p></h3>
            <table cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
            <td class="text-center">
            <!-- <a href="#" class="btn btn-primary" target="_blank">Click here</a> -->
            <!-- <span style="font-size: 16px; font-weight:500;"> to complete the verification</span> -->
            </td>
            </tr>
            </tbody>
            </table>
        </td>';
            $message = $this->emailTemplate($msg);

            $subject = 'Welcome Message';
            $dataM = ['msg' => $message, 'subject' => $subject, 'email' => $request->email];
            $email = new Email();
            $email->composeEmail($dataM);


            return response(['status' => 'success', 'msg' => 'Outlet Created Successfully!']);
        }

        return response(['status' => 'error', 'msg' => 'Outlet Not Created!']);
    }



    private function createUser($outlet_id, $request, $pin)
    {
        $user = new User();
        $user->full_name     = $request->retailer_name;
        $user->email         = $request->email;
        $user->mobile_number = $request->mobile_no;
        $user->password      = Hash::make($request->password);
        $user->role          = ($request->outlet_type =='distributor')?'distributor':'retailer';
        $user->outlet_id     = $outlet_id;
        $user->outlet_name   = $request->outlet_name;
        $user->verify_otp    = 0;
        $user->pin           = $pin;
        $user->distributor_id= Auth::user()->_id;
        $user->save();

        $distrubutor = User::find(Auth::user()->_id);

        $retailers = array();
        if(!empty($distrubutor->retailers))
        $retailers = $distrubutor->retailers;
        $retailers[] =$user->_id;
        $distrubutor->retailers = $retailers;
        $distrubutor->save();
    }


    private function updateUser($outlet_id, $request)
    {
        $user = User::where('outlet_id', $outlet_id)->first();
        $user->full_name     = $request->retailer_name;
        $user->email         = $request->email;
        $user->mobile_number = $request->mobile_no;
        $user->outlet_name   = $request->outlet_name;
        $user->save();
    }

    public function edit(outlet $outlet)
    {

        try {
            return view('distributor.outlet.edit', compact('outlet'));
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function update(DistributorOutletUpdateValidation $request, $id)
    {

        $outlet = Outlet::find($id);
        $tempEmail = $outlet->email;

        $count = $outlet->where('mobile_no', $request->mobile_no)->where('_id', '!=', $id)->count();
        if ($count > 0)
            return response(json_encode(array('validation' => ['mobile_no' => 'This Mobile Number used by Other.'])));

        $count = $outlet->where('email', $request->email)->where('_id', '!=', $id)->count();
        if ($count > 0)
            return response(json_encode(array('validation' => ['email' => 'This Email used by Other.'])));

        $outlet->outlet_type          = 'retailer';
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
        // $outlet->money_transfer_option = $request->money_transfer_option;
        $outlet->status               = $request->status;
        $outlet->account_status       = (int)$request->account_status;

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

        if ($outlet->save()) {

            $this->updateUser($outlet->_id, $request);

            if ($tempEmail != $request->email) {
                $msg = '<td>
         <h5 class="text-center">Outlet details change<.</h3
            <h3 class="otp">Email : ' . $request->email . '</h3>
            <table cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
            <td class="text-center">
            <!-- <a href="#" class="btn btn-primary" target="_blank">Click here</a> -->
            <!-- <span style="font-size: 16px; font-weight:500;"> to complete the verification</span> -->
            </td>
            </tr>
            </tbody>
            </table>
        </td>';

                $message = $this->emailTemplate($msg);
                $subject = 'Details change';
                $dataM = ['msg' => $message, 'subject' => $subject, 'email' => $request->email];
                $email = new Email();
                $email->composeEmail($dataM);
            }

            return response(['status' => 'success', 'msg' => 'Outlet Updated Successfully!']);
        }
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
            $query = Outlet::offset($start)->limit($length);

            if(Auth::user()->role=='distributor')
            $query->where('user_id',Auth::user()->_id);

            $data = $query->orderBy('created', 'DESC')->get();
        }

        $dataArr = [];
        $i = 1;

        foreach ($data as $val) {
            // $action = '<a href="javascript:void(0);" class="text-orange banckModal"  data-toggle="tooltip" data-placement="bottom" title="Bank Charges" outlet_id="' . $val->_id . '"><i class="fas fa-piggy-bank"></i></a>&nbsp;&nbsp;';
            // $action = '<a href="' . url('admin/outlet-bank-charges/' . $val->_id) . '" class="text-orange"  data-toggle="tooltip" data-placement="bottom" title="Bank Charges"><i class="fas fa-piggy-bank"></i></a>&nbsp;&nbsp;';
            $action = '<a href="' . url('distributor/outlets/' . $val->_id . '/edit') . '" class="text-info" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="far fa-edit"></i></a>';

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
            if (!file_exists('sampleCsv'))
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
