<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Http\Validation\TopupValidation;
use App\Models\PaymentMode\BankAccount;
use App\Models\PaymentMode\QrCode;
use App\Models\PaymentMode\Upi;
use App\Models\Topup;
use App\Models\TransferHistory;
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


    public function store(TopupValidation $request)
    {
        $topup = new Topup();
        $topup->retailer_id            = Auth::user()->_id;
        $topup->outlet_id              = Auth::user()->outlet_id;
        $topup->payment_has_code       = uniqCode(16);
        $topup->payment_mode           = $request->payment_mode;
        $topup->payment_reference_id   = $request->payment_reference_id;
        $topup->amount                 = $request->amount;
        $topup->comment                = $request->comment;
        $topup->attachment             = $request->attachment;
        $topup->status                 = 'pending';
        $topup->payment_date           = strtotime($request->payment_date);
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



    public function transactionHistory(Request $request)
    {

        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        $search_arr = $request->search;
        $searchValue = $search_arr['value'];

        // count all data
        $totalRecords = TransferHistory::AllCount();

        if (!empty($searchValue)) {
            // count all data
            $totalRecordswithFilter = TransferHistory::LikeColumn($searchValue);
            $data = TransferHistory::GetResult($searchValue);
        } else {
            // get per page data
            $totalRecordswithFilter = $totalRecords;
            $data = TransferHistory::offset($start)->limit($length)->orderBy('created', 'DESC')->get();
        }
        $dataArr = [];
        $i = 1;

        foreach ($data as $val) {
            $dataArr[] = [
                'sl_no'             => $i,
                'used_amount'       => mSign($val->amount),
                'reciever_name'     => ucwords($val->receiver_name),
                'payment_date'      => date('Y-m-d', $val->payment_date),
                'status'            => $val->status
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


    public function topupHistoryAjax(Request $request)
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

            if ($val->status == 'approved') {
                $payment_has_code = '<a href="javacript:void(0);" class="text-success" data-toggle="tooltip" data-placement="bottom" title="'.$val->admin_comment.'">'.$val->payment_has_code.'</a>';
                $status = '<strong class="text-success">'.ucwords($val->status).'</strong>';
            } else if($val->status =='rejected') {
                $payment_has_code = '<a href="javacript:void(0);" class="text-danger" data-toggle="tooltip" data-placement="bottom" title="'.$val->admin_comment.'">'.$val->payment_has_code.'</a>';
                $status = '<strong class="text-danger">'.ucwords($val->status).'</strong>';
            }else if($val->status == 'pending'){
             $payment_has_code = '<a href="javacript:void(0);" class="text-warning" data-toggle="tooltip" data-placement="bottom" title="'.$val->admin_comment.'">'.$val->payment_has_code.'</a>';
             $status = '<strong class="text-warning">'.ucwords($val->status).'</strong>';
            }

            $dataArr[] = [
                'sr_no'            => $i,
                'payment_has_code' => $payment_has_code,
                'payment_mode'     => ucwords(str_replace('_',' ',$val->payment_mode)),
                'amount'           => mSign($val->amount),
                'status'           => $status,
                'payment_date'     => date('Y-m-d h:i:s A', $val->payment_date),
                'created_date'     => date('Y-m-d', $val->created),
               // 'action'            => $action
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
