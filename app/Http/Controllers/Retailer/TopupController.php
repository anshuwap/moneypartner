<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\PaymentMode\BankAccount;
use App\Models\PaymentMode\QrCode;
use App\Models\PaymentMode\Upi;
use App\Models\Topup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopupController extends Controller
{

    public function index()
    {
        try {
            return view('retailer.topup.list');
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function outletPaymentMode(Request $request)
    {

        $option = '<option value="">Select</option>';
        switch ($request->payment_mode) {
            case "bank_account":
                $selectPayment = BankAccount::get();
                foreach ($selectPayment as $payment) {
                    $option .= '<option value="' . $payment->_id . '">' . $payment->bank_name . '</option>';
                }
                break;
            case "upi_id":
                $selectPayment = Upi::get();
                foreach ($selectPayment as $payment) {
                    $option .= '<option value="' . $payment->_id . '">' . $payment->name . '</option>';
                }
                break;
            case "qr_code":
                $selectPayment = QrCode::get();
                foreach ($selectPayment as $payment) {
                    $option .= '<option value="' . $payment->_id . '">' . $payment->name . '</option>';
                }
                break;
            default:
                $selectPayment = [];
                break;
        }
        die(json_encode($option));
    }


    public function paymentDetails(Request $request)
    {

        switch ($request->payment_mode) {
            case "bank_account":
                $payment = BankAccount::find($request->payment_id);
                $data = '<table class="table table-sm table-bordered">
                <tr>
                    <th>Bank Name</th>
                    <td>'.ucwords($payment->bank_name).'</td>
                </tr>
                <tr>
                    <th>Account Number</th>
                    <td>'.$payment->account_number.'</td>
                </tr>
                <tr>
                    <th>IFSC Code</th>
                    <td>'.$payment->ifsc_code.'</td>
                </tr>
            </table>';
                break;
            case "upi_id":
                $payment = Upi::find($request->payment_id);
                $data = '<table class="table table-sm table-bordered">
                <tr>
                    <th>UPI ID Name</th>
                    <td>'.ucwords($payment->name).'</td>
                </tr>
                <tr>
                    <th>UPI ID</th>
                    <td>'.$payment->upi_id.'</td>
                </tr>
            </table>';
                break;
            case "qr_code":
                $payment = QrCode::find($request->payment_id);
                $data = '<div class="card w-50 py-4 m-auto">
                <img src="'.asset('attachment/payment_mode/'.$payment->qr_code).'">
                <div class="text-center"><span>'.ucwords($payment->name).'</span></div>
                </div>';
                break;
            default:
                $data = '<div>Not Found Any Data!</div>';
                break;
        }
        die(json_encode($data));
    }


    public function store(Request $request)
    {
        $topup = new Topup();
        $topup->retailer_id            = Auth::user()->_id;
        $topup->payment_mode           = $request->payment_mode;
        $topup->payment_reference_id   = $request->payment_reference_id;
        $topup->amount                 = $request->amount;
        $topup->comment                = $request->comment;
        $topup->attachment             = $request->attachment;
        //for file uploade
        if (!empty($request->file('attachment')))
            $topup->attachment  = singleFile($request->file('attachment'), 'attachment/payment_request_proff');

        if ($topup->save())
            return response(['status' => 'success', 'msg' => 'Your Request is Successfully for Topup!']);

        return response(['status' => 'error', 'msg' => 'Your Topup Request is Failed, Please Try Again!']);
    }


    public function show(Topup $Topup)
    {
    }


    public function edit(Topup $Topup)
    {

        try {
            die(json_encode($Topup));
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function update(Request $request, Topup $Topup)
    {

        $qr_code = $Topup;
        $qr_code->name         = $request->name;
        $qr_code->status       = $request->status;
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
            $res = Topup::where('_id', $id)->delete();
            if ($res)
                return response(['status' => 'success', 'msg' => 'QR Code Removed Successfully!']);

            return response(['status' => 'error', 'msg' => 'QR Code not Removed!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }


    public function TopupStatus(Request $request)
    {

        try {
            $Topup = Topup::find($request->id);
            $Topup->status = (int)$request->status;
            $Topup->save();
            if ($Topup->status == 1)
                return response(['status' => 'success', 'msg' => 'This QR Code is Active!', 'val' => $Topup->status]);

            return response(['status' => 'success', 'msg' => 'This QR Code is Inactive!', 'val' => $Topup->status]);
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
        $totalRecords = Topup::AllCount();

        if (!empty($searchValue)) {
            // count all data
            $totalRecordswithFilter = Topup::LikeColumn($searchValue);
            $data = Topup::GetResult($searchValue);
        } else {
            // get per page data
            $totalRecordswithFilter = $totalRecords;
            $data = Topup::offset($start)->limit($length)->orderBy('created', 'DESC')->get();
        }
        $dataArr = [];
        $i = 1;

        foreach ($data as $val) {
            $action = '<a href="javascript:void(0);" class="text-info edit_qr_code" data-toggle="tooltip" data-placement="bottom" title="Edit" qr_code_id="' . $val->_id . '"><i class="far fa-edit"></i></a>&nbsp;&nbsp;';
            $action .= '<a href="javascript:void(0);" class="text-danger remove_qr_code"  data-toggle="tooltip" data-placement="bottom" title="Remove" qr_code_id="' . $val->_id . '"><i class="fas fa-trash"></i></a>';
            if ($val->status == 1) {
                $status = ' <a href="javascript:void(0);"><span class="badge badge-success activeVer" id="active_' . $val->_id . '" _id="' . $val->_id . '" val="0">Active</span></a>';
            } else {
                $status = ' <a href="javascript:void(0)"><span class="badge badge-danger activeVer" id="active_' . $val->_id . '" _id="' . $val->_id . '" val="1">Inactive</span></a>';
            }
            $url = asset('attachment/payment_mode/' . $val->qr_code);
            $dataArr[] = [
                'sl_no'             => $i,
                'name'              => ucwords($val->name),
                'qr_code'           => '<img src="' . $url . '" style="height: 50px; width: 55px;">',
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


    public function topupHistory(){
        try {
            return view('retailer.topup.topup_history');
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }
}
