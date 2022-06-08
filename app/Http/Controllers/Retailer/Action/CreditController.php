<?php

namespace App\Http\Controllers\Retailer\Action;

use App\Http\Controllers\Controller;
use App\Models\CreditDebit;
use App\Models\PaymentChannel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{

    public function index(Request $request)
    {
        try {

            $query = CreditDebit::where('type', 'credit')->where('retailer_id',Auth::user()->_id);

            if (!empty($request->payment_channel))
                $query->where('channel', $request->payment_channel);

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
            $data['credits'] = $query->paginate($perPage);
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();
            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();
            return view('retailer.action.credit', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function export(Request $request)
    {
        try {
            $file_name = 'manual-credit';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $passbookArray = ['Transaction Id', 'Channel', 'Amount','UTR No','Paid Status', 'Created Date', 'Created By', 'Modified By', 'Modified Date'];
            fputcsv($f, $passbookArray, $delimiter); //put heading here

            $query = CreditDebit::where('type', 'credit')->where('retailer_id',Auth::user()->_id);

            if (!empty($request->payment_channel))
                $query->where('channel', $request->payment_channel);

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

            $credits = $query->get();

            if ($credits->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $passbookArr = [];
            foreach ($credits as $credit) {

                $passbook_val[] = $credit->transaction_id;
                $passbook_val[] = $credit->channel;
                $passbook_val[] = $credit->amount;
                $passbook_val[] = $credit->utr_no;
                $passbook_val[] = ucwords($credit->paid_status);
                $passbook_val[] = date('Y-m-d H:i', $credit->created);
                $passbook_val[] = !empty($credit->UserName['full_name']) ? $credit->UserName['full_name'] : '';
                $passbook_val[] = !empty($credit->ModifiedBy['full_name']) ? $credit->ModifiedBy['full_name'] : '';
                $passbook_val[] = !empty($credit->action_date) ? date('d M Y H:i', $credit->action_date) : '';

                $passbookArr = $passbook_val;

                fputcsv($f, $passbookArr, $delimiter); //put heading here
                $passbook_val = [];
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
