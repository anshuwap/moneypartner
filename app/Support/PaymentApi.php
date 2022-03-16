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
            'PaymentMode'   => 'IMPS',
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
                    'payment_mode'   => 'payunie-Preet Kumar'
                ],
                'status' => 'success'
            ];
        }
        return $result;
    }

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
            'PaymentMode'   => 'IMPS',
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
                    'payment_mode'   => 'payunie-Parveen'
                ],
                'status' => 'success'
            ];
        }
        return $result;
    }


    function pay2All($input)
    {

        $input = (object)$input;
        $post_data   = array(
            'mobile_number'    => $input->mobile_number,
            'amount'           => $input->amount,
            'beneficiary_name' => $input->receiver_name,
            'account_number'   => $input->account_number,
            'ifsc'             => $input->ifsc_code,
            'channel_id'       => "2",
            'PaymentMode'      => 'IMPS',
            'client_id'        => 'UNI' . uniqCode(4),
            'provider_id'      => "143",
        );

print_r('{
    "mobile_number":"' . $input->mobile_number . '",
    "amount":"' . $input->amount . '",
    "beneficiary_name":"' . $input->receiver_name . '",
    "account_number": "' . $input->account_number . '",
    "ifsc":"' . $input->ifsc_code . '",
    "channel_id":"2",
    "client_id":"pay' . uniqCode(4) . '",
    "provider_id":"143"
}');
die;
        $data = json_encode($post_data);
        echo $data;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.pay2all.in/v1/payout/transfer',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "mobile_number":"' . $input->mobile_number . '",
    "amount":"' . $input->amount . '",
    "beneficiary_name":"' . $input->receiver_name . '",
    "account_number": "' . $input->account_number . '",
    "ifsc":"' . $input->ifsc_code . '",
    "channel_id":"2",
    "client_id":"pay' . uniqCode(4) . '",
    "provider_id":"143"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImE1OWU3ZDllOWIyOTc4ZTljMDdmN2IxZmQ0ZWUxN2NmODExYTMwMDc3NTk1NjE1YTg5MjY1MWQyYWJhMGE4NGVjZTU3M2Y2NTIxYzlkYzJmIn0.eyJhdWQiOiIxIiwianRpIjoiYTU5ZTdkOWU5YjI5NzhlOWMwN2Y3YjFmZDRlZTE3Y2Y4MTFhMzAwNzc1OTU2MTVhODkyNjUxZDJhYmEwYTg0ZWNlNTczZjY1MjFjOWRjMmYiLCJpYXQiOjE2NDY1MDE0NzksIm5iZiI6MTY0NjUwMTQ3OSwiZXhwIjoxNjc4MDM3NDc5LCJzdWIiOiI1MzMiLCJzY29wZXMiOltdfQ.E1G8vjsvSNhp9wONJCwo8n_XuvalYI4j36YBiGnZcuhNjpagzoyCgTyrUttYYGoDVFguH2e6NDWFQyBiS2Il0dL6mtjs3Sl-ChQqSwXicqUJJ54ownC-w2YSo2v0EXlyhZc3oDH6vd3pLhfOf1xA1MasRZn7DEZ9FmtxzP977kpC8jFykvYrX3XI2Yk5EX2SRTWa1KaT9W9ghbMUHiYWX8VPlrTAx2FZq_r606CTeWiyu0J5ygAF7iwzwnsDHYCAQzmaUAR7VzBF3P5K-e2cpKsZwtaWjsduUjMMvjJWJIzTErXTQsEdk1uFU3uLUqn8OWbe0Cjn3Y5LRutLbsnR2BV4UR3SkfHDl6KS-Evjiy8uhDrUDg0v7k2Gy1lsJdRZx3-phNC9XHac2x_hnWpGXtMv16yo_POX6FrhiSnv_UlYq1uJqjD1FLtHHdglyScFwPUxqFJyfH9PZek6HTqdFfl0-1YhqyBb29DeCmewh5ySxFx2pnSTSxm-m0-7wFWPF5nw1uxrketjKJR7zKDOopV8WU4-UYuOiQ2MPyLm83647Xtr3iwwkruePy4yjLfiLgaABEfS16c_YopFoQt1pVnMADjitqv2WD5vi0DVieAq9v_K14b39DQ5bM6503JGXoR9FOZd3ck_uVxr_e3xcoOGwmQeqeriPDMFB1p8oKM',
                'Content-Type: application/json'
            ),
        ));
        $http_result = curl_exec($curl);
        print_r($http_result);
        $res = json_decode($http_result);
        curl_close($curl);
        print_r($res);
        die;
        if ($res['status'] === 1 || $res['status'] === 0) {
            $result = [
                'response' => [
                    'utr_number'     => $res['utr'],
                    'status'         => "success",
                    'status_id'      => $res['status_id'],
                    'report_id'      => $res['report_id'],
                    'orderid'        => $res['orderid'],
                    'msg'            => $res['message'],
                    'payment_mode'   => 'Pay2All-Parveen'
                ],
                'status' => 'success'
            ];
        }


        if ($res['status'] === 2) {
            $result = [
                'response' => [
                    'utr_number'     => $res['utr'],
                    'status'         => "failed",
                    'status_id'      => $res['status_id'],
                    'report_id'      => $res['report_id'],
                    'orderid'        => $res['orderid'],
                    'msg'            => $res['message'],
                    'payment_mode'   => 'Pay2ALL'
                ],
                'status' => 'error'
            ];
        }

        if ($res['status'] === 3) {
            $result = [
                'response' => [
                    'utr_number'     => $res['utr'],
                    'status'         => "pending",
                    'status_id'      => $res['status_id'],
                    'report_id'      => $res['report_id'],
                    'orderid'        => $res['orderid'],
                    'msg'            => $res['message'],
                    'payment_mode'   => 'Pay2ALL'
                ],
                'status' => 'error'
            ];
        }

        return $result;
    }
}
