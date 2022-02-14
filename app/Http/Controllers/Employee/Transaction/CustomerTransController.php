<?php

namespace App\Http\Controllers\Employee\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\PaymentChannel;
use App\Models\Transaction\CustomerTrans;
use App\Models\TransactionComment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerTransController extends Controller
{

    public function index(Request $request)
    {
        try {

            $outlets = Outlet::select('_id', 'outlet_name')->where('account_status', "1")->orderBy('created', 'ASC')->get();
            $outlet_id = $request->outlet_id;
            if (empty($request->outlet_id))
                $outlet_id = $outlets[0]->_id;

            $query = CustomerTrans::query()->where('outlet_id', $outlet_id);

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = strtotime(trim($request->start_date) . " 00:00:00");
                $end_date = strtotime(trim($request->end_date) . " 23:59:59");
            } else {
                $crrMonth = (date('Y-m-d'));
                $start_date = strtotime(trim(date("d-m-Y", strtotime('-30 days', strtotime($crrMonth)))) . " 00:00:00");
                $end_date = strtotime(trim(date('Y-m-d')) . " 23:59:59");
            }

            $query->whereBetween('created', [$start_date, $end_date]);

            $data['customer_trans'] = $query->get();

            $data['outlets']   = $outlets;
            $data['outlet_id'] = $outlet_id;
            $data['start_date'] = $start_date;
            $data['end_date']  = $end_date;

            //for payment channel
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();

            return view('employee.transaction.customer_display', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function store(Request $request)
    {

        $CustomerTrans = CustomerTrans::find($request->trans_id);

        $trans_details = $CustomerTrans->trans_details;

        $trans_details[$request->key]['admin_action'] = $request->admin_action;
        $trans_details[$request->key]['status']       = $request->status;
        $trans_details[$request->key]['updated']      = strtotime(date('Y-m-d H:i:s'));
        $trans_details[$request->key]['admin_action'] = 1;

        $CustomerTrans->trans_details = $trans_details;

        if (!$CustomerTrans->save())
            return response(['status' => 'error', 'msg' => 'Transaction Request not Created!']);

        if ($CustomerTrans->trans_details[$request->key]['status'] == 'approved') {

            $amount        = $CustomerTrans->trans_details[$request->key]['amount'];
            $receiver_name = $CustomerTrans->trans_details[$request->key]['receiver_name'];
            $payment_date  = $CustomerTrans->trans_details[$request->key]['created'];
            $status        = $CustomerTrans->trans_details[$request->key]['status'];
            $retailer_id   = $CustomerTrans->_id;

            //add total amount in customer trans collection
            $customer_trans = CustomerTrans::find($retailer_id);
            $total_amount   = ($customer_trans->total_amount) + ($amount);
            $customer_trans->total_amount = $total_amount;
            $customer_trans->save();
            //insert data in transfer history collection
            transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status);
        } else {
            //add toupup amount here
            $retailer_id      = $CustomerTrans->retailer_id;
            $transaction_fees = $CustomerTrans->trans_details[$request->key]['transaction_fees'];
            $amount           = $CustomerTrans->trans_details[$request->key]['amount'];
            addTopupAmount($retailer_id, $amount, $transaction_fees, 1);
        }

        return response(['status' => 'success', 'msg' => 'Transaction ' . ucwords($CustomerTrans->status) . ' Successfully!']);
    }


    public function edit(CustomerTrans $CustomerTrans)
    {

        try {

            die(json_encode($CustomerTrans));
        } catch (Exception $e) {
            return redirect('500');
        }
    }



    public function viewDetail(Request $request)
    {

        try {
            $CustomerTrans = CustomerTrans::find($request->id);
            $details = (object) $CustomerTrans->trans_details[$request->key];

            $table = '<table class="table table-sm table-hover border-0">
                <tr>
                <td>Sender Name :</td>
                <td>' . $details->sender_name . '</td>
                </tr>

                <td>Amount :</td>
                <td>' . mSign($details->amount) . '</td>
                </tr>

                <td>Transaction Fees :</td>
                <td>' . mSign($details->transaction_fees) . '</td>
                </tr>

                <td>Receiver Name :</td>
                <td>' . $details->receiver_name . '</td>
                </tr>

                <td>Payment Mode :</td>
                <td>' . ucwords(str_replace('_', ' ', $details->payment_mode)) . '</td>
                </tr>';

            $payment = $details->payment_channel;
            $i = 1;
            foreach ($payment as $key => $paym) {
                $click = "copyToClipboard('#text-" . $i . "','#copy-" . $i . "')";
                $table .= '<th>' . ucwords(trim(str_replace('_', ' ', $key), "'")) . ' :</th>
                <td><span class="text-success text" id="text-' . $i . '">' . $paym . '</span>&nbsp;&nbsp;
                <span><a href="javascript:void(0);" onClick="' . $click . '" class="text-danger"><i class="fas fa-copy"></i></a></span>
                <span class="ml-4 d-none" id="copy-' . $i . '"><i class="fas fa-check-circle text-success"></i>Copied</span></td>
                </tr>';
                $i++;
            }


            $clicka = "copyToClipboard('#text-t','#copy-t')";
            $table .= '<th>Total Amount :</th>
                <td><span class="text-success text" id="text-t">' . $details->amount + $details->transaction_fees . '</span>&nbsp;&nbsp;
                <span><a href="javascript:void(0);" onClick="' . $clicka . '" class="text-danger"><i class="fas fa-copy"></i></a></span>
                <span class="ml-4 d-none" id="copy-t"><i class="fas fa-check-circle text-success"></i>Copied</span></td>
                </tr>';


            switch ($details->status) {
                case 'approved':
                    $status = '<span class="badge badge-success">' . ucwords($details->status) . '</span>';
                    break;
                case 'reject':
                    $status = '<span class="badge badge-danger">' . ucwords($details->status) . '</span>';
                    break;
                default:
                    $status = '<span class="badge badge-warning">' . ucwords($details->status) . '</span>';
                    break;
            }

            $table .= '<td>Status :</td>
                <td>' . $status . '</td>
                </tr>';

            if (!empty($details->pancard_no))
                $table .= '<td>Pancard No :</td>
                <td>' . $details->pancard_no . '</td>
                </tr>';

            if (!empty($details->pancard))
                $table .= '<td>Pancard :</td>
                <td>' . $details->pancard . '</td>
                </tr>';

            $table .= ' <td>Created :</td>
                <td>' . date('d M Y', $details->created) . '</td>
                </tr>
                </table>';

            die(json_encode($table));
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'something went wrong']);
        }
    }



    public function customerComment(Request $request)
    {

        $type = $request->type;
        $commments = TransactionComment::where('type', $type)->get();

        $option = '<option value="">Select</option>';
        foreach ($commments as $comment) {
            $option .= '<option value="' . $comment->comment . '">' . $comment->comment . '</option>';
        }

        die(json_encode($option));
    }


    public function ajaxList(Request $request)
    {

        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        $search_arr = $request->search;
        $searchValue = $search_arr['value'];

        // count all data
        $totalRecords = CustomerTrans::AllCount();

        if (!empty($searchValue)) {
            // count all data
            $totalRecordswithFilter = CustomerTrans::LikeColumn($searchValue);
            $data = CustomerTrans::GetResult($searchValue);
        } else {
            // get per page data
            $totalRecordswithFilter = $totalRecords;
            $data = CustomerTrans::offset($start)->limit($length)->orderBy('created', 'DESC')->get();
        }
        $dataArr = [];
        $i = 1;

        foreach ($data as $val) {
            $action = '<a href="javascript:void(0);" class="btn btn-info btn-sm customer_trans"  _id="' . $val->_id . '">Action</a>';
            // $action .= '<a href="javascript:void(0);" class="text-danger remove_bank_account"  data-toggle="tooltip" data-placement="bottom" title="Remove" bank_account_id="' . $val->_id . '"><i class="fas fa-trash"></i></a>';

            if ($val->status == 'approved') {
                $status = '<strong class="text-success">' . ucwords($val->status) . '</strong>';
            } else if ($val->status == 'rejected') {
                $status = '<strong class="text-danger">' . ucwords($val->status) . '</strong>';
            } else if ($val->status == 'pending') {
                $status = '<strong class="text-warning">' . ucwords($val->status) . '</strong>';
            }

            $dataArr[] = [
                'sl_no'             => $i,
                'sender_name'       => ucwords($val->sender_name),
                'mobile_number'     => $val->mobile_number,
                'amount'            => mSign($val->amount),
                'receiver_name'     => $val->receiver_name,
                'payment_mode'      => ucwords(str_replace('_', ' ', $val->payment_mode)),
                'status'            => $status,
                'created_date'      => date('Y-m-d', $val->created),
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
