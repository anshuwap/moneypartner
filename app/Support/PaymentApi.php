<?php

namespace App\Support;

class PaymentApi
{

    function payunie($input)
    {
        $input = (object)$input;
        $post_data   = array(
            'key'           => 'HsFNUzHnm2MQszwQT383c8sWS',
            'AccountNumber' => $input->account_number,
            'IFSC'          => $input->ifsc_code,
            'Amount'        => $input->amount,
            'HolderName'    => $input->receiver_name,
            'BankName'      => $input->bank_name,
            'TransactionID' => 'UNI' . uniqCode(4),
            'Remark'        => 'paid',
        );

        $url = "https://payunie.com/api/v1/payout";

        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        $http_result = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($http_result);

        if ($res[0]->status !== 'Accepted')
        $result = ['status' => 'error', 'msg' => $res[0]->statusMessage];

        if ($res[0]->status === 'Accepted') {
            $result = [
                'response' => [
                    'utr_number'     => $res[0]->UTRNUMBER,
                    'transaction_id' => $res[0]->TransactionID,
                    'msg'            => $res[0]->statusMessage,
                    'payment_mode'   => 'payunie'
                ],
                'status' => 'success'
            ];
        }
        return $result;
    }


    // function payunieApi1($input)
    // {
    //     $input = (object)$input;
    //     $post_data   = array(
    //         'key'           => 'HsFNUzHnm2MQszwQT383c8sWS',
    //         'AccountNumber' => $input->account_number,
    //         'IFSC'          => $input->ifsc_code,
    //         'Amount'        => $input->amount,
    //         'HolderName'    => $input->receiver_name,
    //         'BankName'      => $input->bank_name,
    //         'TransactionID' => 'UNI' . uniqCode(4),
    //         'Remark'        => 'paid',
    //     );

    //     $url = "https://payunie.com/api/v1/payout";

    //     $ch  = curl_init();
    //     curl_setopt($ch, CURLOPT_VERBOSE, 1);
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_FAILONERROR, 0);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    //     $http_result = curl_exec($ch);
    //     curl_close($ch);

    //    $res = json_decode($http_result);

    //     if ($res[0]->status !== 'Accepted')
    //     $result = ['status' => 'error', 'msg' => $res[0]->statusMessage];

    //     if ($res[0]->status === 'Accepted') {
    //         $result = [
    //             'response' => [
    //                 'utr_number'     => $res[0]->UTRNUMBER,
    //                 'transaction_id' => $res[0]->TransactionID,
    //                 'msg'            => $res[0]->statusMessage,
    //                 'payment_mode'   => 'payunie'
    //             ],
    //             'status' => 'success'
    //         ];
    //     }
    //     return $result;
    // }

    function payunie1($input)
    {
        $input = (object)$input;
        $post_data   = array(
            'key'           => 'vPxce3A8W23XTokxvBbj34Co',
            'AccountNumber' => $input->account_number,
            'IFSC'          => $input->ifsc_code,
            'Amount'        => $input->amount,
            'HolderName'    => $input->receiver_name,
            'BankName'      => $input->bank_name,
            'TransactionID' => 'UNI' .uniqCode(4),
            'Remark'        => 'paid',
        );

        $url = "https://payunie.com/api/v1/payout";

        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        $http_result = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($http_result);

        if ($res[0]->status !== 'Accepted')
        $result = ['status' => 'error', 'msg' => $res[0]->statusMessage];

        if ($res[0]->status === 'Accepted') {
            $result = [
                'response' => [
                    'utr_number'     => $res[0]->UTRNUMBER,
                    'transaction_id' => $res[0]->TransactionID,
                    'msg'            => $res[0]->statusMessage,
                    'payment_mode'   => 'payunie'
                ],
                'status' => 'success'
            ];
        }
        return $result;
    }
}
