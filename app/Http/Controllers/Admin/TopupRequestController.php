<?php

namespace App\Http\Controllers\Admin\PaymentMode;

use App\Http\Controllers\Controller;
use App\Models\PaymentMode\QrCode;
use App\Models\Topup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopupRequestController extends Controller
{

    public function topupRequest(Request $request)
    {
        try {
            $topup = Topup::find($request->id);
            $topup->status = $request->topup;
            $topup->admin_comment = $request->comment;
            $topup->save();
            if($topup->status =='apprived'){
            return response(['status'=>'success','msg'=>'Topup Request Apprived']);
            }else if($topup->status =='rejected'){
            return response(['status'=>'success','msg'=>'Topup Request Rejected']);
            }else{
            return response(['status'=>'error','msg'=>'Something Went Wrong']);
            }
        } catch (Exception $e) {
            return response(['status'=>'error','msg'=>config('error.codeException')]);
        }
    }


    public function topupRequestDetials($id){

        try {
            $topup = Topup::find($id);


        } catch (Exception $e) {
            return response(['status'=>'error','msg'=>config('error.codeException')]);
        }
    }
}
