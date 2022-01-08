<?php

namespace App\Http\Controllers\Admin\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Transaction\RetailerTrans;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetailerTransController extends Controller
{

    public function index(Request $request)
    {
        try {

            $outlets = Outlet::select('_id','outlet_name')->where('account_status',"1")->orderBy('created','ASC')->get();
            $outlet_id = $request->outlet_id;
            if(empty($request->outlet_id))
            $outlet_id =$outlets[0]->_id;

            $query = RetailerTrans::query()->where('outlet_id',$outlet_id);

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = strtotime(trim($request->start_date) . " 00:00:00");
                $end_date = strtotime(trim($request->end_date) . " 23:59:59");
            }else {
                $crrMonth = (date('Y-m-d'));
                $start_date = strtotime(trim(date("d-m-Y", strtotime('-30 days', strtotime($crrMonth)))) . " 00:00:00");
                $end_date = strtotime(trim(date('Y-m-d')) . " 23:59:59");
            }

           $query->whereBetween('created', [$start_date, $end_date]);

            $data['retailerTrans'] = $query->get();
            $data['outlets']   = $outlets;
            $data['outlet_id'] = $outlet_id;
            $data['start_date']= $start_date;
            $data['end_date']  = $end_date;
            return view('admin.transaction.retailer_display',$data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function store(Request $request)
    {

        $retailerTrans = RetailerTrans::find($request->trans_id);
        $retailerTrans->status       = $request->status;
        $retailerTrans->admin_action = $request->admin_action;

        if (!$retailerTrans->save())
            return response(['status' => 'error', 'msg' => 'Transaction Request not  Created!']);

        if ($retailerTrans->status == 'approved') {
            $amount        = $retailerTrans->amount;
            $receiver_name = $retailerTrans->receiver_name;
            $payment_date  = $retailerTrans->created;
            $status        = $retailerTrans->status;
            $retailer_id   = $retailerTrans->_id;

            //insert data in transfer history collection
            transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status);
        } else {
            //add toupup amount here
            $retailer_id      = $retailerTrans->retailer_id;
            $transaction_fees = $retailerTrans->transaction_fees;
            $amount           = $retailerTrans->amount;
            addTopupAmount($retailer_id, $amount, $transaction_fees, 1);
        }
        return response(['status' => 'success', 'msg' => 'Transaction ' . ucwords($retailerTrans->status) . ' Successfully!']);
    }



}
