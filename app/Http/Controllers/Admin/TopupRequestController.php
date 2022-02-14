<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\PaymentMode\BankAccount;
use App\Models\PaymentMode\QrCode;
use App\Models\PaymentMode\Upi;
use App\Models\Topup;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopupRequestController extends Controller
{


    public function index(Request $request)
    {
        try {

            //  $topups = Topup::get();

            $outlets = Outlet::select('_id', 'outlet_name')->where('account_status', "1")->orderBy('created', 'ASC')->get();
            $outlet_id = $request->outlet_id;
            if (empty($request->outlet_id))
                $outlet_id = $outlets[0]->_id;

            $query = Topup::query()->where('outlet_id', $outlet_id);

            if (empty($request->outlet_id)) {
                $query = Topup::query();
                $outlet_id = '';
            }

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = strtotime(trim($request->start_date) . " 00:00:00");
                $end_date = strtotime(trim($request->end_date) . " 23:59:59");
            } else {
                $crrMonth = (date('Y-m-d'));
                $start_date = strtotime(trim(date("d-m-Y", strtotime('-30 days', strtotime($crrMonth)))) . " 00:00:00");
                $end_date = strtotime(trim(date('Y-m-d')) . " 23:59:59");
            }

            $topups = $query->whereBetween('created', [$start_date, $end_date])->get();


            $topup_request = [];
            foreach ($topups as $topup) {

                $topup_request[] = (object)[
                    'id'           => $topup->_id,
                    'retailer_name' => !empty($topup->RetailerName['full_name'])?$topup->RetailerName['full_name']:'',
                    'amount'       => mSign($topup->amount),
                    'payment_mode' => ucwords(str_replace('_', " ", $topup->payment_mode)),
                    'status'       => ucwords($topup->status),
                    'payment_date' => date('y-m-d h:i:s A', $topup->payment_date),
                    'admin_action' => $topup->admin_action,
                    'comment'      => $topup->comment
                ];
            }
            $data['topup_request'] = $topup_request;
            $data['outlets']   = $outlets;
            $data['outlet_id'] = $outlet_id;

            return view('admin.topup_request.list', $data);
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function topupRequest(Request $request)
    {
        try {
            $topup = Topup::find($request->id);
            $topup->status = $request->status;
            $topup->admin_comment = $request->comment;
            $topup->admin_action = 1;
            $topup->save();
            if ($topup->status == 'approved') {

                $topups = Topup::select('amount')->where('status', 'approved')->where('outlet_id', $topup->outlet_id)->get();

                $amount = 0;
                foreach ($topups as $topupa) {
                    $amount += $topupa->amount;
                }

                //add topup amount in retailer wallet
                addTopupAmount($topup->retailer_id, $amount);

                return response(['status' => 'success', 'msg' => 'Topup Request Approved', 'status_msg' => ucwords($topup->status), 'id' => $topup->id]);
            } else if ($topup->status == 'rejected') {
                return response(['status' => 'success', 'msg' => 'Topup Request Rejected', 'status_msg' => ucwords($topup->status), 'id' => $topup->id]);
            } else {
                return response(['status' => 'error', 'msg' => 'Something Went Wrong']);
            }
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function topupRequestDetials($id)
    {

        try {
            $topup = Topup::find($id);

            $show_action = (empty($topup->admin_action) && $topup->admin_action == 0) ? 0 : 1;

            switch ($topup->payment_mode) {
                case "bank_account":
                    $payment = BankAccount::find($topup->payment_reference_id);
                    $data = '<div>Request Details</div>
                    <table class="table table-sm table-bordered">
                    <tr>
                        <th>Payment Mode</th>
                        <td>' . ucwords(str_replace('_', ' ', $topup->payment_mode)) . '</td>
                    </tr>
                    <tr>
                        <th>Bnak Name</th>
                        <td>' . ucwords($payment->bank_name) . '</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>' . mSign($topup->amount) . '</td>
                    </tr>
                    <tr>
                    <th>Comment</th>
                    <td>' . $topup->comment . '</td>
                </tr>
                <tr>
                <th>Attachment</th>
                <td><a href="' . asset("attachment/payment_request_proff/$topup->attachment") . '" target="_blank">' . $topup->attachment . '</a></td>
            </tr>
                </table>
                    <div><h6>Destination Details</h6></div>
                    <table class="table table-sm table-bordered">
                    <tr>
                        <th>Bank Name</th>
                        <td>' . ucwords($payment->bank_name) . '</td>
                    </tr>
                    <tr>
                        <th>Account Number</th>
                        <td>' . $payment->account_number . '</td>
                    </tr>
                    <tr>
                        <th>IFSC Code</th>
                        <td>' . $payment->ifsc_code . '</td>
                    </tr>
                </table>';
                    break;
                case "upi_id":
                    $payment = Upi::find($topup->payment_reference_id);

                    $data = '<div>Request Details</div>
                    <table class="table table-sm table-bordered">
                    <tr>
                        <th>Payment Mode</th>
                        <td>' . ucwords(str_replace('_', ' ', $topup->payment_mode)) . '</td>
                    </tr>
                    <tr>
                        <th>UPI Name</th>
                        <td>' . ucwords($payment->name) . '</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>' . $topup->amount . '</td>
                    </tr>
                    <tr>
                    <th>Comment</th>
                    <td>' . $topup->comment . '</td>
                </tr>
                <tr>
                <th>Attachment</th>
                <td><a href="' . asset("attachment/payment_request_proff/$topup->attachment") . '" target="_blank">' . $topup->attachment . '</a></td>
            </tr>
                </table>
                    <div><h6>Destination Details</h6></div><table class="table table-sm table-bordered">
                    <tr>
                        <th>UPI ID Name</th>
                        <td>' . ucwords($payment->name) . '</td>
                    </tr>
                    <tr>
                        <th>UPI ID</th>
                        <td>' . $payment->upi_id . '</td>
                    </tr>
                </table>';
                    break;
                case "qr_code":
                    $payment = QrCode::find($topup->payment_reference_id);
                    $data = '<div>Request Details</div>
                    <table class="table table-sm table-bordered">
                    <tr>
                        <th>Payment Mode</th>
                        <td>' . ucwords(str_replace('_', ' ', $topup->payment_mode)) . '</td>
                    </tr>
                    <tr>
                        <th>Qrcode Name Name</th>
                        <td>' . ucwords($payment->qr_code) . '</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>' . $topup->amount . '</td>
                    </tr>
                    <tr>
                    <th>Comment</th>
                    <td>' . $topup->comment . '</td>
                </tr>
                <tr>
                <th>Attachment</th>
                <td><a href="' . asset("attachment/payment_request_proff/$topup->attachment") . '" target="_blank">' . $topup->attachment . '</a></td>

            </tr>
                </table>
                    <div><h6>Destination Details</h6></div>
                    <div class="card w-50 py-4 m-auto">
                    <img src="' . asset('attachment/payment_mode/' . $payment->qr_code) . '">
                    <div class="text-center"><span>' . ucwords($payment->name) . '</span></div>
                    </div>';
                    break;
                default:
                    $data = '<div>Not Found Any Data!</div>';
                    break;
            }


            die(json_encode(['data' => $data, 'show_action' => $show_action]));
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => config('custom_errorlist.error.codeException')]);
        }
    }
}
