<?php

namespace App\Http\Controllers\Admin\PaymentMode;

use App\Http\Controllers\Controller;
use App\Models\PaymentMode\QrCode;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrCodeController extends Controller
{

    public function index()
    {
        try {
            return view('admin.payment_mode.qr_code');
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function create()
    {
    }


    public function store(Request $request)
    {
        $qr_code = new QrCode();
        $qr_code->user_id      = Auth::user()->_id;
        $qr_code->name         = $request->name;
        $qr_code->status       = (int)$request->status;
        //for file uploade
        if (!empty($request->file('qr_code')))
            $qr_code->qr_code  = singleFile($request->file('qr_code'), 'attachment/payment_mode');

        if ($qr_code->save())
            return response(['status' => 'success', 'msg' => 'QR Code Added Successfully!']);

        return response(['status' => 'error', 'msg' => 'QR Code not Added Successfully!']);
    }

    public function edit(QrCode $QrCode)
    {

        try {
            die(json_encode($QrCode));
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function update(Request $request, QrCode $QrCode)
    {

        $qr_code = $QrCode;
        $qr_code->name         = $request->name;
        $qr_code->status       = (int)$request->status;
        //for file uploade
        if (!empty($request->file('qr_code')))
            $qr_code->qr_code  = singleFile($request->file('qr_code'), 'attachment/payment_mode');


        if ($qr_code->save())
            return response(['status' => 'success', 'msg' => 'QR Code Updated Successfully!']);

        return response(['status' => 'error', 'msg' => 'QR Code not Updated Successfully!']);
    }


    public function destroy($id)
    {
        try {
            $res = QrCode::where('_id', $id)->delete();
            if ($res)
                return response(['status' => 'success', 'msg' => 'QR Code Removed Successfully!']);

            return response(['status' => 'error', 'msg' => 'QR Code not Removed!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }


    public function QrCodeStatus(Request $request)
    {

        try {
            $qrCode = QrCode::find($request->id);
            $qrCode->status = (int)$request->status;
            $qrCode->save();
            if ($qrCode->status == 1)
                return response(['status' => 'success', 'msg' => 'This QR Code is Active!', 'val' => $qrCode->status]);

            return response(['status' => 'success', 'msg' => 'This QR Code is Inactive!', 'val' => $qrCode->status]);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }


    public function allocateRetailer(Request $request)
    {
        try {
            $_id = $request->id;
            $qrCode = QrCode::select('retailer_ids')->find($_id);

            $retailers = User::select('_id','outlet_name','role')->whereIn('role', ['retailer','distributor'])->get();

            $checkbox  = '<table class="table table-sm">';
            $checkbox .= '<tr><th>Outlet Name</th><th>Outlet Type</th></th><th>Action</th></tr>';
            foreach ($retailers as $retailer) {
                $checked = (!empty($qrCode->retailer_ids) && is_array($qrCode->retailer_ids) && in_array($retailer->id,$qrCode->retailer_ids))?"checked":"";

                $checkbox .= '<tr>';
                $checkbox .='<td>' . ucwords($retailer->outlet_name) . '</td>';
                $checkbox .='<td>'.ucwords($retailer->role).'</td>';
                $checkbox .='<td><input type="checkbox" value="' . $retailer->_id . '" name="retailers[]" '.$checked.'></td>';
                $checkbox .='</tr>';
            }
            $checkbox .= '</table>';

            die(json_encode($checkbox));
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }


    public function saveAllocateRetailer(Request $request)
    {
        try {
            $qrCode = QrCode::find($request->id);
            $qrCode->retailer_ids = $request->retailers;
            if ($qrCode->save())
                return response(['status' => 'success', 'msg' => 'Bank Account Allocated Successfully!']);

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
        $totalRecords = QrCode::AllCount();

        if (!empty($searchValue)) {
            // count all data
            $totalRecordswithFilter = QrCode::LikeColumn($searchValue);
            $data = QrCode::GetResult($searchValue);
        } else {
            // get per page data
            $totalRecordswithFilter = $totalRecords;
            $data = QrCode::offset($start)->limit($length)->orderBy('created', 'DESC')->get();
        }
        $dataArr = [];
        $i = 1;

        foreach ($data as $val) {
                 $action = '<a href="javascript:void(0);" class="text-warning allocate-retailer" data-toggle="tooltip" data-placement="bottom" title="Edit" bank_account_id="' . $val->_id . '"><i class="far fa-eye"></i></a>&nbsp;&nbsp;';
            $action .= '<a href="javascript:void(0);" class="text-info edit_qr_code" data-toggle="tooltip" data-placement="bottom" title="Edit" qr_code_id="' . $val->_id . '"><i class="far fa-edit"></i></a>&nbsp;&nbsp;';
           // $action .= '<a href="javascript:void(0);" class="text-danger remove_qr_code"  data-toggle="tooltip" data-placement="bottom" title="Remove" qr_code_id="' . $val->_id . '"><i class="fas fa-trash"></i></a>';
            if ($val->status == 1) {
                $status = ' <a href="javascript:void(0);"><span class="badge badge-success activeVer" id="active_' . $val->_id . '" _id="' . $val->_id . '" val="0">Active</span></a>';
            } else {
                $status = ' <a href="javascript:void(0)"><span class="badge badge-danger activeVer" id="active_' . $val->_id . '" _id="' . $val->_id . '" val="1">Inactive</span></a>';
            }
            $url =asset('attachment/payment_mode/'.$val->qr_code);
            $dataArr[] = [
                'sl_no'             => $i,
                'name'              => ucwords($val->name),
                'qr_code'           =>'<img src="'.$url.'" style="height: 50px; width: 55px;">',
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
