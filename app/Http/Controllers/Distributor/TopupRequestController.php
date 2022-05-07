<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\PaymentChannel;
use App\Models\PaymentMode\BankAccount;
use App\Models\PaymentMode\QrCode;
use App\Models\PaymentMode\Upi;
use App\Models\Topup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopupRequestController extends Controller
{
    public function index(Request $request)
    {
        try {

            //  $topups = Topup::get();
            $outlets = Outlet::select('_id', 'outlet_name')->where('user_id', Auth::user()->_id)->where('account_status', 1)->orderBy('created', 'DESC')->get();

            $query = Topup::query();

            if (!empty($request->outlet_id))
                $query->where('outlet_id', $request->outlet_id);

            if (!empty($request->transaction_id))
                $query->where('payment_id', $request->transaction_id);

            if (!empty($request->status))
                $query->where('status', $request->status);

            if (!empty($request->payment_by))
                $query->where('payment_by', $request->payment_by);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }
            $query->whereBetween('created', [$start_date, $end_date]);
            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $topups = $query->whereIn('retailer_id', Auth::user()->retailers)->orderBy('created', 'DESC')->paginate($perPage);

            $data['topup_request'] = $topups;
            $data['outlets']   = $outlets;

            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();

            return view('distributor.topup_request.list', $data);
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function pendingRequest(Request $request)
    {
        try {

            $outlets = Outlet::select('_id', 'outlet_name')->where('user_id', Auth::user()->_id)->where('account_status', 1)->orderBy('created', 'DESC')->get();

            $query = Topup::query();

            if (!empty($request->outlet_id))
                $query->where('outlet_id', $request->outlet_id);

            if (!empty($request->transaction_id))
                $query->where('payment_id', $request->transaction_id);

            if (!empty($request->payment_by))
                $query->where('payment_by', $request->payment_by);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }
            $query->where('status', 'pending')->whereBetween('created', [$start_date, $end_date]);
            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $topups = $query->whereIn('retailer_id', Auth::user()->retailers)->orderBy('created', 'DESC')->paginate($perPage);

            $data['topup_request'] = $topups;
            $data['outlets']   = $outlets;

            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();

            return view('distributor.topup_request.pending_topup', $data);
        } catch (Exception $e) {
            return redirect('500');
        }
    }

    public function topupRequest(Request $request)
    {
        try {
            $topup = Topup::find($request->id);
            $topup->status = $request->status;
            $topup->admin_comment   = $request->comment;
            $topup->payment_channel = $request->payment_channel;
            $topup->admin_action = 1;
            $topup->action_by    = Auth::user()->_id;
            $topup->action_date  = time();
            $topup->save();
            if ($topup->status == 'success') {


                $amount = 0;
                if (!empty($topup->amount))
                    $amount = $topup->amount;

                //add topup amount in retailer wallet
                addTopupAmount($topup->retailer_id, $amount);

                $retailer_id      = $topup->retailer_id;
                $amount           = $topup->amount;
                $receiver_name    = '';
                $payment_date     = $topup->payment_date;
                $status           = $topup->status;
                $payment_mode     = $topup->payment_mode;
                $type             = $topup->payment_mode;
                $transaction_fees = 0;
                //insert data in transfer history collection
                transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'credit');

                return response(['status' => 'success', 'msg' => 'Topup Request Success', 'status_msg' => ucwords($topup->status), 'id' => $topup->id]);
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

            $payment_channel = PaymentChannel::select('_id', 'name')->get();
            $option = '';
            foreach ($payment_channel as $channel) {
                $option .= '<option value="' . $channel->name . '">' . $channel->name . '</option>';
            }

            $change_channel = '<div class="row" id="utr">
                        <div class="col-md-12 form-group">
                        <label>Update Payment Channel</label>
                        <input type="hidden" value="' . $topup->_id . '" id="topup-id" name="id">
                        <div class="input-group input-group-sm">
                       <select name="payment_channel" class="form-control form-control-sm" id="payment_channel">
                     <option value="">Select Payment Channel</option>
                    ' . $option . '
                   </select>
                            <span class="input-group-append ">
                                <button type="button" class="btn btn-danger btn-flat" id="update_payment_channel">Update</button>
                            </span>
                        </div>
                    </div>
                </div>';

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
                </table>' . $change_channel;
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
                </table>' . $change_channel;
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
                    </div>' . $change_channel;
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


    public function updatePaymentChannel(Request $request)
    {
        try {
            $topup = Topup::find($request->id);
            $topup->payment_channel = $request->payment_channel;
            if ($topup->save())
                return response(['status' => 'success', 'msg' => 'Payment Channel updated successfully!']);

            return response(['status' => 'error', 'msg' => 'Payment Channel not updated!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function export(Request $request)
    {

        try {
            $file_name = 'topup-report';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $transactionArray = [
                'Transaction ID', 'Outlet Name', 'UTR No.', 'Channel', 'Amount', 'Payment Mode', 'Payment In',
                'Requested Date', 'Approve/Reject By', 'Approve/Reject Date', 'Status'
            ];
            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query = Topup::query();

            if (!empty($request->outlet_id))
                $query->where('outlet_id', $request->outlet_id);

            if (!empty($request->transaction_id))
                $query->where('payment_id', $request->transaction_id);

            if (!empty($request->payment_by))
                $query->where('payment_by', $request->payment_by);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }

            $query->whereBetween('created', [$start_date, $end_date]);
            $topups = $query->whereIn('retailer_id', Auth::user()->retailers)->get();

            if ($topups->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $transactionArr = [];
            foreach ($topups as $topup) {

                $topup_val[] = $topup->payment_id;
                $topup_val[] = !empty($topup->RetailerName['outlet_name']) ? $topup->RetailerName['outlet_name'] : '';
                $topup_val[] = $topup->utr_no;
                $topup_val[] = !empty($topup->payment_channel) ? ucwords(str_replace('_', ' ', $topup->payment_channel)) : '';
                $topup_val[] = $topup->amount;
                $topup_val[] = $topup->payment_by;
                $topup_val[] = !empty($topup->payment_mode) ? $topup->paymentModeName($topup->payment_mode, $topup->payment_reference_id) : '';
                $topup_val[] = !empty($topup->payment_date) ? date('Y-m-d H:i:s', $topup->payment_date) : '';
                $topup_val[] = !empty($topup->UserName['full_name']) ? $topup->UserName['full_name'] : '-';
                $topup_val[] = !empty($topup->action_date) ? date('Y-m-d H:i:s', $topup->action_date) : '';
                $topup_val[] = strtoupper($topup->status);

                $transactionArr = $topup_val;

                fputcsv($f, $transactionArr, $delimiter); //put heading here
                $topup_val = [];
            }
            // Move back to beginning of file
            fseek($f, 0);

            // headers to download file
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $file_name . '.csv"');
            readfile('exportCsv/' . $file_name . '.csv');

            //remove file form server
            $path = 'exportCsv/' . $file_name . '.csv';
            if (file_exists($path))
                unlink($path);
        } catch (Exception $e) {
            return redirect('500');
        }
    }




    public function pendingExport(Request $request)
    {
        try {
            $file_name = 'pending-topup-report';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $transactionArray = [
                'Transaction ID', 'Outlet Name', 'UTR No', 'Amount', 'Payment Mode', 'Payment In', 'Requested Date',
                'Status'
            ];
            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query = Topup::query()->where('status', 'pending');

            if (!empty($request->transaction_id))
                $query->where('payment_id', $request->transaction_id);

            if (!empty($request->payment_by))
                $query->where('payment_by', $request->payment_by);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }

            $topups = $query->whereBetween('created', [$start_date, $end_date])->orderBy('created', 'DESC')->get();

            if ($topups->isEmpty())
                return back()->with('error', 'There is no any record for export!');;

            $transactionArr = [];
            foreach ($topups as $topup) {

                $topup_val[] = $topup->payment_id;
                $topup_val[] = !empty($topup->RetailerName['outlet_name']) ? $topup->RetailerName['outlet_name'] : '';
                $topup_val[] = $topup->utr_no;
                $topup_val[] = $topup->amount;
                $topup_val[] = $topup->payment_by;
                $topup_val[] = !empty($topup->payment_mode) ? $topup->paymentModeName($topup->payment_mode, $topup->payment_reference_id) : '';
                $topup_val[] = date('Y-m-d H:i:s', $topup->payment_date);
                $topup_val[] = strtoupper($topup->status);
                $transactionArr = $topup_val;

                fputcsv($f, $transactionArr, $delimiter); //put heading here
                $topup_val = [];
            }
            // Move back to beginning of file
            fseek($f, 0);

            // headers to download file
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $file_name . '.csv"');
            readfile('exportCsv/' . $file_name . '.csv');

            //remove file form server
            $path = 'exportCsv/' . $file_name . '.csv';
            if (file_exists($path))
                unlink($path);
        } catch (Exception $e) {
            return redirect('500');
        }
    }
}
