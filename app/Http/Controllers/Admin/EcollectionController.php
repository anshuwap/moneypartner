<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Validation\EcollectionValidation;
use App\Models\Api\ECollection;
use App\Models\Outlet;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class EcollectionController extends Controller
{

    public function index(Request $request)
    {
        try {

            $outlets = Outlet::select('_id', 'outlet_name')->where('account_status', 1)->orderBy('created', 'DESC')->get();
            $query = ECollection::query();

            if (!empty($request->outlet_id))
                $query->where('outlet_id', $request->outlet_id);

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
            $data['ecollections'] = $query->paginate($perPage);
            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();
            $data['outlets'] = $outlets;
            return view('admin.e_collection.display', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function store(Request $request)
    {
        try {
            $ecollection_id = $request->id;
            $ecollection = ECollection::find($ecollection_id);
            if (!empty($ecollection)) {
                $outlet_id = $ecollection->outlet_id;
                $user = User::select('_id')->where('outlet_id', $outlet_id)->first();
                $retailer_id = $user->_id;
                $amount = $ecollection->amount;
                $status = 'success';
                $payment_mode = 'E-Collection';
                $remark     = 'Credited by E-Collection';
                $payment_date = time();

                //add topup amount in retailer wallet
                addTopupAmount($retailer_id, $amount);

                $retailer_id      = $retailer_id;
                $amount           = $amount;
                $receiver_name    = '';
                $payment_date     = $payment_date;
                $status           = $status;
                $payment_mode     = $payment_mode;
                $type             = $payment_mode;
                $transaction_fees = 0;
                //insert data in transfer history collection
                transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'credit', $remark);

                $ecollection->wallet_status = 'approved';
                $ecollection->remark        = $remark;
                $ecollection->save();
                return response(['status' => 'success', 'msg' => mSign($amount) . ' Credited Successfully', 'status_msg' => ucwords($status)]);
            } else {
                return response(['status' => 'error', 'msg' => "Something went wrong!"]);
            }
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
}
