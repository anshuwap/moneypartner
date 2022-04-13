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

        $result = [];
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
        if ($res[0]->status === 'Failed') {
            $result = [
                'response' => [
                    'msg'            => $res[0]->statusMessage,
                    'payment_mode'   => 'payunie-Preet Kumar'
                ],
                'status' => 'failed'
            ];
        }

        if ($res[0]->status !== 'Accepted' || $res[0]->status !== 'Failed')
            $result = [
                'response' => [
                    'msg'            => $res[0]->statusMessage,
                    'payment_mode'   => 'payunie-Preet Kumar',
                    'transaction_id' => (!empty($res[0]->TransactionID)) ? $res[0]->TransactionID : '',
                    // 'msg' =>(!empty($res[0]->error['MESSAGE']))?$res[0]->error['MESSAGE']:''
                ],
                'status' => 'process',
                'insufficient' => $res[0]->statusMessage
            ];

        return $result;
    }

    function payunie1($input)
    {
        $input = (object)$input;
        $post_data   = array(
            'key'           => 'cGb25SnErgsFSyiLCAAba9m9',
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

        $result = [];
        if ($res[0]->status === 'Accepted') {
            $result = [
                'response' => [
                    'utr_number'     => $res[0]->UTRNUMBER,
                    'transaction_id' => $res[0]->TransactionID,
                    'msg'            => $res[0]->statusMessage,
                    'payment_mode'   => 'payunie-Rashid Ali'
                ],
                'status' => 'success'
            ];
        }

        if ($res[0]->status === 'Failed') {
            $result = [
                'response' => [
                    'msg'            => $res[0]->statusMessage,
                    'payment_mode'   => 'payunie-Rashid Ali'
                ],
                'status' => 'failed'
            ];
        }

        if ($res[0]->status !== 'Accepted' || $res[0]->status !== 'Failed')
            $result = [
                'response' => [
                    'msg'            => $res[0]->statusMessage,
                    'payment_mode'   => 'payunie-Rashid Ali',
                    'transaction_id' => (!empty($res[0]->TransactionID)) ? $res[0]->TransactionID : '',
                ],
                'status' => 'process',
                'insufficient' => $res[0]->statusMessage
            ];
// print_r($result);die;
        return $result;
    }


    function pay2All($input)
    {

        $curl1 = curl_init();

        curl_setopt_array($curl1, array(
            CURLOPT_URL => 'https://api.pay2all.in/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "email":"parveenbinjhol70759@gmail.com",
    "password":"Parveen@123"
   }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl1);

        curl_close($curl1);

        $res = json_decode($response);
        $token  = $res->access_token;
        if (empty($token)) {
            return  $result = [
                'response' => [
                    'status'         => "failed",
                    'msg'            => 'Token not Genrated',
                    'payment_mode'   => 'Pay2All-Parveen'
                ],
                'status' => 'failed'
            ];
        }
        $client_id = uniqCode(4);
        $input = (object)$input;
        $post_data   = array(
            'mobile_number'    => $input->mobile_number,
            'amount'           => $input->amount,
            'beneficiary_name' => $input->receiver_name,
            'account_number'   => $input->account_number,
            'ifsc'             => $input->ifsc_code,
            'channel_id'       => "2",
            'client_id'        =>  $client_id,
            'provider_id'      => "143",
        );
        $data = json_encode($post_data);

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
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $res = (array)json_decode($response);
// print_r($res);die;
        $result = [];

        if (!empty($res) && empty($res['errors'])) {
            if ($res['status'] == 0 || $res['status'] == 1) {
                return $result = [
                    'response' => [
                        'utr_number'     => $res['utr'],
                        'status'         => "success",
                        'status_id'      => $res['status_id'],
                        'report_id'      => $res['report_id'],
                        'orderid'        => $res['orderid'],
                        'msg'            => $res['message'],
                        'client_id'      => $client_id,
                        'payment_mode'   => 'Pay2All-Parveen'
                    ],
                    'status' => 'success'
                ];
            }
        }


        if (!empty($res['status']) && $res['status'] == 2) {
            return $result = [
                'response' => [
                    'utr_number'     => $res['utr'],
                    'status'         => "failed",
                    'status_id'      => $res['status_id'],
                    'report_id'      => $res['report_id'],
                    'orderid'        => $res['orderid'],
                    'msg'            => $res['message'],
                    'payment_mode'   => 'Pay2All-Parveen'
                ],
                'status' => 'failed'
            ];
        }

        if (!empty($res['status']) && $res['status'] == 3) {
            return $result = [
                'response' => [
                    'utr_number'     => $res['utr'],
                    'status'         => "pending",
                    'status_id'      => $res['status_id'],
                    'report_id'      => $res['report_id'],
                    'orderid'        => $res['orderid'],
                    'msg'            => $res['message'],
                    'client_id'      => $client_id,
                    'payment_mode'   => 'Pay2All-Parveen'
                ],
                'status' => 'process'
            ];
        }

        if (!empty($res) && !empty($res['errors'])) {

            $msg = '';
            $key = 0;
            foreach ($res['errors'] as $error) {
                $msg .= $error[$key++];
            }
            return $result = [
                'response' => [
                    'msg'            => $msg,
                    'status'         => "failed",
                    'payment_mode'   => 'Pay2All-Parveen'
                ],
                'status' => 'failed'
            ];
        }
        return $result;
    }



    function checkStatusPayunie($trans_id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://payunie.com/api/v1/payoutstatus',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "{
            'key':'HsFNUzHnm2MQszwQT383c8sWS',
            'TransactionID':'$trans_id'
            }",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);

        $result = [];
        if ($res[0]->statusMessage != 'Success' && $res[0]->statusMessage != 'Fail') {
            $result = [
                'response' => [
                    'msg'            => $res[0]->statusMessage,
                    'payment_mode'   => 'payunie-Preet Kumar'
                ],
                'status' => 'process'
            ];
        }

        if ($res[0]->statusMessage === 'Success') {
            $result = [
                'response' => [
                    'msg'            => $res[0]->statusMessage,
                    'utr_number'     => $res[0]->UTRNUMBER,
                    'payment_mode'   => 'payunie-Preet Kumar'
                ],
                'status' => 'success'
            ];
        }

        if ($res[0]->statusMessage === 'Fail') {
            $result = [
                'response' => [
                    'msg'            => $res[0]->statusMessage,
                    'payment_mode'   => 'payunie-Preet Kumar'
                ],
                'status' => 'failed'
            ];
        }

        return $result;
    }



    function checkStatusPayunie1($trans_id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://payunie.com/api/v1/payoutstatus',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "{
            'key':'cGb25SnErgsFSyiLCAAba9m9',
            'TransactionID':'$trans_id'
            }",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);


        $result = [];
        if ($res[0]->statusMessage != 'Success' && $res[0]->statusMessage != 'Fail') {
            $result = [
                'response' => [
                    'msg'            => $res[0]->statusMessage,
                    'payment_mode'   => 'payunie-Rashid Ali'
                ],
                'status' => 'process'
            ];
        }

        if ($res[0]->statusMessage === 'Success') {
            $result = [
                'response' => [
                    'msg'            => $res[0]->statusMessage,
                    'utr_number'     => $res[0]->UTRNUMBER,
                    'payment_mode'   => 'payunie-Rashid Ali'
                ],
                'status' => 'success'
            ];
        }

        if ($res[0]->statusMessage === 'Fail') {
            $result = [
                'response' => [
                    'msg'            => $res[0]->statusMessage,
                    'payment_mode'   => 'payunie-Rashid Ali'
                ],
                'status' => 'failed'
            ];
        }

        return $result;
    }


    function checkStatusPay2All($client_id)
    {
        $curl1 = curl_init();

        curl_setopt_array($curl1, array(
            CURLOPT_URL => 'https://api.pay2all.in/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "email":"parveenbinjhol70759@gmail.com",
    "password":"Parveen@123"
}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl1);

        curl_close($curl1);

        $res = json_decode($response);
        $token  = $res->access_token;
        if (empty($token)) {
            return  $result = [
                'response' => [
                    'status'         => "failed",
                    'msg'            => 'Token not Genrated',
                    'payment_mode'   => 'Pay2All-Parveen'
                ],
                'status' => 'failed'
            ];
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.pay2all.in/v1/payment/status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "client_id":' . $client_id . '
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $response1 = curl_exec($curl);

        curl_close($curl);
        $res = (array)json_decode($response1);

        $result = [];
        if (empty($res)) {
            return $result = [
                'response' => [
                    'status'         => "pending",
                    'msg'            => 'not found any response',
                    'payment_mode'   => 'Pay2All-Parveen'
                ],
                'status' => 'process'
            ];
        }

        if ($res['status'] === 1 || $res['status'] === 0) {
            $result = [
                'response' => [
                    'utr_number'     => $res['utr'],
                    'status'         => "success",
                    'number'         => $res['number'],
                    'status_id'      => $res['status_id'],
                    'report_id'      => $res['report_id'],
                    'client_id'      => $res['client_id'],
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
                    'payment_mode'   => 'Pay2All-Parveen'
                ],
                'status' => 'failed'
            ];
        }

        if ($res['status'] === 3) {
            $result = [
                'response' => [
                    'utr_number'     => $res['utr'],
                    'status'         => "pending",
                    'number'         => $res['number'],
                    'status_id'      => $res['status_id'],
                    'report_id'      => $res['report_id'],
                    'client_id'      => $res['client_id'],
                    'payment_mode'   => 'Pay2All-Parveen'
                ],
                'status' => 'process'
            ];
        }

        return $result;
    }
}
