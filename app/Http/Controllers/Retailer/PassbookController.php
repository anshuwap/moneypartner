<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\TransferHistory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassbookController extends Controller
{

    public function index(Request $request)
    {
        try {

            $start_date = '';
            $end_date   = '';
            if (!empty($request->date_range)) {
                $date = explode('-', $request->date_range);
                $start_date = $date[0];
                $end_date   = $date[1];
            }
            $query = TransferHistory::where('retailer_id', Auth::user()->_id);

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $crrMonth = (date('Y-m-d'));
                $start_date = strtotime(trim(date("d-m-Y", strtotime('-30 days', strtotime($crrMonth)))) . " 00:00:00");
                $end_date   = strtotime(trim(date('Y-m-d')) . " 23:59:59");
            }

            if(!empty($request->type))
            $query->where('type',$request->type);

            $query->whereBetween('created', [$start_date, $end_date]);

            $data['passbook'] = $query->paginate(config('constants.perPage'));
            $request->request->remove('page');
            $data['filter']  = $request->all();
            return view('retailer.passbook.list', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }
}
