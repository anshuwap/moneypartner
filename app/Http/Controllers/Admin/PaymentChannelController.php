<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentChannel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentChannelController extends Controller
{

    public function index()
    {
        try {
            return view('admin.payment_channel.list');
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function create()
    {
    }


    public function store(Request $request)
    {
        $paymnetChannel = new PaymentChannel();
        $paymnetChannel->user_id      = Auth::user()->_id;
        $paymnetChannel->name         = $request->name;
        $paymnetChannel->status       = $request->status;

        if ($paymnetChannel->save())
            return response(['status' => 'success', 'msg' => 'Payment Channel Added Successfully!']);

        return response(['status' => 'error', 'msg' => 'Payment Channel not Added Successfully!']);
    }



    public function edit(PaymentChannel $PaymentChannel)
    {

        try {
            die(json_encode($PaymentChannel));
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function update(Request $request, PaymentChannel $PaymentChannel)
    {

        $paymentChannel = $PaymentChannel;
        $paymentChannel->name         = $request->name;
        $paymentChannel->status       = $request->status;

        if ($paymentChannel->save())
            return response(['status' => 'success', 'msg' => 'Payment Channel Updated Successfully!']);

        return response(['status' => 'error', 'msg' => 'Payment Channel not Updated Successfully!']);
    }


    public function destroy($id)
    {
        try {
            $res = PaymentChannel::where('_id', $id)->delete();
            if ($res)
                return response(['status' => 'success', 'msg' => 'Payment Channel Removed Successfully!']);

            return response(['status' => 'error', 'msg' => 'Payment Channel not Removed!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }


    public function paymentChannelStatus(Request $request)
    {

        try {
            $paymentChannel = PaymentChannel::find($request->id);
            $paymentChannel->status = (int)$request->status;
            $paymentChannel->save();
            if ($paymentChannel->status == 1)
                return response(['status' => 'success', 'msg' => 'This Payment Channel is Active!', 'val' => $paymentChannel->status]);

            return response(['status' => 'success', 'msg' => 'This Payment Channel is Inactive!', 'val' => $paymentChannel->status]);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }


    public function ajaxList(Request $request)
    {

        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        $search_arr = $request->search;
        $searchValue = $search_arr['value'];

        // count all data
        $totalRecords = PaymentChannel::AllCount();

        if (!empty($searchValue)) {
            // count all data
            $totalRecordswithFilter = PaymentChannel::LikeColumn($searchValue);
            $data = PaymentChannel::GetResult($searchValue);
        } else {
            // get per page data
            $totalRecordswithFilter = $totalRecords;
            $data = PaymentChannel::offset($start)->limit($length)->orderBy('created', 'DESC')->get();
        }
        $dataArr = [];
        $i = 1;

        foreach ($data as $val) {
            $action = '<a href="javascript:void(0);" class="text-info edit_paymnet_channel" data-toggle="tooltip" data-placement="bottom" title="Edit" paymnet_channel_id="' . $val->_id . '"><i class="far fa-edit"></i></a>&nbsp;&nbsp;';
            // $action .= '<a href="javascript:void(0);" class="text-danger remove_paymnet_channel"  data-toggle="tooltip" data-placement="bottom" title="Remove" paymnet_channel_id="' . $val->_id . '"><i class="fas fa-trash"></i></a>';
            if ($val->status == 1) {
                $status = ' <a href="javascript:void(0);"><span class="badge badge-success activeVer" id="active_' . $val->_id . '" _id="' . $val->_id . '" val="0">Active</span></a>';
            } else {
                $status = ' <a href="javascript:void(0)"><span class="badge badge-danger activeVer" id="active_' . $val->_id . '" _id="' . $val->_id . '" val="1">Inactive</span></a>';
            }
            $dataArr[] = [
                'sl_no'             => $i,
                'name'              => ucwords($val->name),
                'created_date'      => date('Y-m-d', $val->created),
                'status'            => $status,
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
