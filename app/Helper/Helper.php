<?php

use App\Models\Outlet;
use App\Models\TransferHistory;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;

if (!function_exists('uniqCode')) {
    function uniqCode($lenght)
    {
        // uniqCode
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return strtoupper(substr(bin2hex($bytes), 0, $lenght));
    }
}

if (!function_exists('singleFile')) {

    function singleFile($file, $folder)
    {
        if ($file) {
            if (!file_exists($folder))
                mkdir($folder, 0777, true);

            $destinationPath = public_path() . '/' . $folder;
            $profileImage = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move($destinationPath, $profileImage);
            $fileName = "$profileImage";
            return $fileName;
        }
        return false;
    }
}


if (!function_exists('pr')) {
    function pr($data)
    {
        echo "<pre>";
        print_r($data);
        echo '</pre>';
        die;
    }
}


if (!function_exists('profileImage')) {

    function profileImage()
    {
        $outlet_id = Auth::user()->outlet_id;
        $outlet = Outlet::select('profile_image')->find($outlet_id);
        return (!empty($outlet->profile_image)) ? asset('attachment') . '/' . $outlet->profile_image : asset('assets/profile/37.jpg');
    }
}


if (!function_exists('employeeImage')) {

    function employeeImage()
    {
        $user_id = Auth::user()->_id;
        $user = User::select('employee_img')->find($user_id);
        return (!empty($user->employee_img)) ? asset('attachment') . '/' . $user->employee_img : asset('assets/profile/37.jpg');
    }
}

if (!function_exists('transferHistory')) {
    function transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $transaction_type, $fees, $type, $remark = '')
    {

        $closing_amount = 0;
        $A_amount = User::select('available_amount', 'outlet_id')->find($retailer_id);
        if (!empty($A_amount))
            $closing_amount = $A_amount->available_amount;

        $transferHistory = new TransferHistory();
        $transferHistory->retailer_id   = $retailer_id;
        $transferHistory->outlet_id     = $A_amount->outlet_id;
        $transferHistory->amount        = $amount;
        $transferHistory->receiver_name = $receiver_name;
        $transferHistory->payment_date  = $payment_date;
        $transferHistory->status        = $status;
        $transferHistory->payment_mode  = $payment_mode;
        $transferHistory->fees          = $fees;
        $transferHistory->type          = $type;
        $transferHistory->transaction_type = $transaction_type;
        $transferHistory->closing_amount = $closing_amount;
        $transferHistory->remark        = $remark;
        $transferHistory->save();
    }
}




if (!function_exists('mSign')) {
    function mSign($val)
    {

        $val = ($val) ? number_format($val, 2, '.', '') : 0;

        return '<i class="fas fa-rupee-sign" style="font-size: 13px;
    color: #696b74;"></i>&nbsp;' . $val;
    }
}

if (!function_exists('spentTopupAmount')) {
    function spentTopupAmount($user_id, $amount)
    {
        try {
            $user = User::find($user_id);
            $avaliable_amount = ($user->available_amount) - ($amount);

            $spent_amount = ($user->spent_amount) + ($amount);

            $user->available_amount = $avaliable_amount;
            $user->spent_amount = $spent_amount;

            if ($user->save())
                return true;

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}


if (!function_exists('addTopupAmount')) {
    function addTopupAmount($user_id, $amount, $transaction_fees = 0, $reject = 0)
    {

        try {
            $user = User::find($user_id);

            $avaliable_amount = ($user->available_amount) + ($amount);

            $total_amount = ($user->total_amount) + ($amount);
            $spent_amount = $user->spent_amount;

            if ($reject && !empty($spent_amount)) {
                $t_amount = ($amount) + ($transaction_fees);
                $user->spent_amount = ($spent_amount) - ($t_amount);
                $avaliable_amount = ($avaliable_amount) + ($transaction_fees);
            } else {
                $user->total_amount     = $total_amount;
            }
            $user->available_amount = $avaliable_amount;

            if ($user->save())
                return true;

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}


if (!function_exists('MoneyPartnerOption')) {

    function MoneyPartnerOption()
    {
       
        $outlet = Outlet::select('money_transfer_option')->find(Auth::user()->outlet_id);
        if (!empty($outlet))
            return (object)$outlet->money_transfer_option;

        return false;
    }
}


//for push data to using webhook url
function webhook($data)
{
    //get webhook url
    $webhook = Webhook::select('webhook_url')->where('retailer_id', $data->retailer_id)->first();

    $url = '';
    if (!empty($webhook))
        $url = $webhook->webhook_url;
    if (!empty($url)) {
        $response = Curl::to($url)
            ->withData(json_encode($data))
            ->post();
        return true;
    }
}

function verify_url($base_url)
{
    $url = Webhook::where('retailer_id', Auth::user()->_id)->where('type', 'base_url')->first();
    if (!empty($url->base_url) && $url->base_url == $base_url)
        return false;

    return true;
}
