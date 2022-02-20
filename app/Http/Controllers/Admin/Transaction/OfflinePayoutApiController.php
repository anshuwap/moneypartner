<?php

namespace App\Http\Controllers\Admin\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Api\OfflinePayoutApi;
use App\Models\PaymentChannel;
use App\Models\TransactionComment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfflinePayoutApiController extends Controller
{

    public function index(Request $request)
    {
        try {

            $outlets = Outlet::select('_id', 'outlet_name')->where('account_status', "1")->orderBy('created', 'ASC')->get();
            $outlet_id = $request->outlet_id;
            if (empty($request->outlet_id))
                $outlet_id = $outlets[0]->_id;

            $query = OfflinePayoutApi::query()->where('outlet_id', $outlet_id);

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = strtotime(trim($request->start_date) . " 00:00:00");
                $end_date = strtotime(trim($request->end_date) . " 23:59:59");
            } else {
                $crrMonth = (date('Y-m-d'));
                $start_date = strtotime(trim(date("d-m-Y", strtotime('-30 days', strtotime($crrMonth)))) . " 00:00:00");
                $end_date = strtotime(trim(date('Y-m-d')) . " 23:59:59");
            }

            $query->whereBetween('created', [$start_date, $end_date]);

             $data['offlinePayouts'] = $query->get();
            $data['outlets']         = $outlets;
            $data['outlet_id']       = $outlet_id;
            $data['start_date']      = $start_date;
            $data['end_date']        = $end_date;

             //for payment channel
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();
            return view('admin.transaction.offline_payout_api', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


   public function store(Request $request)
    {

        $offlinePayout = OfflinePayoutApi::find($request->trans_id);
        $offlinePayout->status       = $request->status;
        $offlinePayout->admin_action = $request->admin_action;

        if (!$offlinePayout->save())
            return response(['status' => 'error', 'msg' => 'Transaction Request not  Created!']);

        if ($offlinePayout->status == 'approved') {
            $amount        = $offlinePayout->amount;
            $receiver_name = $offlinePayout->receiver_name;
            $payment_date  = $offlinePayout->created;
            $status        = $offlinePayout->status;
            $payment_mode  = $offlinePayout->payment_mode;
            $transaction_fees = $offlinePayout->transaction_fees;

            $retailer_id   = $offlinePayout->retailer_id;

      //insert data in transfer history collection
            transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status,$payment_mode,$transaction_fees,'debit');
        } else {
            //add toupup amount here
            $retailer_id      = $offlinePayout->retailer_id;
            $transaction_fees = $offlinePayout->transaction_fees;
            $amount           = $offlinePayout->amount;
            addTopupAmount($retailer_id, $amount, $transaction_fees, 1);
        }
        return response(['status' => 'success', 'msg' => 'Transaction ' . ucwords($offlinePayout->status) . ' Successfully!']);
    }




    public function viewDetail(Request $request)
    {

        try {
            $details = OfflinePayoutApi::find($request->id);

            $table = '<table class="table table-sm">
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


    public function Comment(Request $request)
    {

        $type = $request->type;
        $commments = TransactionComment::where('type', $type)->get();

        $option = '<option value="">Select</option>';
        foreach ($commments as $comment) {
            $option .= '<option value="' . $comment->comment . '">' . $comment->comment . '</option>';
        }

        die(json_encode($option));
    }

}
