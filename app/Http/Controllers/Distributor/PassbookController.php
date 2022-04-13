<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\TransferHistory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassbookController extends Controller
{

    public function index(Request $request)
    {
        try {
            $outlets = Outlet::select('_id', 'outlet_name')->where('user_id', Auth::user()->_id)->where('account_status', 1)->orderBy('created', 'DESC')->get();

            $query = TransferHistory::query();
            if (!empty($request->outlet_id))
                $query->where('outlet_id', $request->outlet_id);

            if (!empty($request->type))
                $query->where('type', $request->type);

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
            $data['passbook'] = $query->whereIn('retailer_id',Auth::user()->retailers)->orderBy('created', 'DESC')->paginate($perPage);

            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

            $data['outlets'] = $outlets;

            return view('distributor.passbook.list', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function export(Request $request)
    {
        try {
            $file_name = 'payment-history';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $passbookArray = ['Outlet Name', 'Transaction Time', 'Payment Mode', 'Transaction Amount', 'Fees', 'Closing Amount', 'Credit/Debit', 'Status'];
            fputcsv($f, $passbookArray, $delimiter); //put heading here

            $start_date = '';
            $end_date   = '';
            if (!empty($request->date_range)) {
                $date = explode('-', $request->date_range);
                $start_date = $date[0];
                $end_date   = $date[1];
            }

            $query = TransferHistory::query();
            if (!empty($request->outlet_id))
                $query->where('outlet_id', $request->outlet_id);

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $crrMonth = (date('Y-m-d'));
                $start_date = strtotime(trim(date("d-m-Y", strtotime('-30 days', strtotime($crrMonth)))) . " 00:00:00");
                $end_date   = strtotime(trim(date('Y-m-d')) . " 23:59:59");
            }

            $query->whereBetween('created', [$start_date, $end_date]);

            if (!empty($request->type))
                $query->where('type', $request->type);

            $passbooks = $query->whereIn('retailer_id',Auth::user()->retailers)->get();

            if ($passbooks->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $passbookArr = [];
            foreach ($passbooks as $passbook) {

                $passbook_val[] = (!empty($passbook->OutletName['outlet_name'])) ? ucwords($passbook->OutletName['outlet_name']) : '';
                $passbook_val[] = date('Y-m-d H:i:s A', $passbook->created);
                $passbook_val[] = ucwords(str_replace('_', ' ', $passbook->payment_mode));
                $passbook_val[] = $passbook->amount;
                $passbook_val[] = (!empty($passbook->fees)) ? $passbook->fees : '-';
                $passbook_val[] = $passbook->closing_amount;
                $passbook_val[] = strtoupper($passbook->type);
                $passbook_val[] = strtoupper($passbook->status);

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
