<?php

namespace App\Support;

use App\Models\OdnimoBeneficiaryModal;

class ClicknCash
{
    function payout($input)
    {
        $input = (object)$input;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.clickncash.in/app/api/v9/money_transfer',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'name' => $input->receiver_name,
                'mobile' => $input->mobile_number,
                'email' => Auth()->user()->email,
                'amount' =>  $input->amount,
                'account_number' => $input->account_number,
                'ifsc_code' => $input->ifsc_code,
                'bank_name' => $input->bank_name,
                'agent_id' => uniqCode(6),
                'user_id' => 'RPAY3cc6902ca2',
                'token' => 'b71de66bec7f1ee9ef2661cff78011bb',
                'mode' => 'IMPS'
            ),
            CURLOPT_HTTPHEADER => array(
                'Cookie: ci_session=8a530848b499ae5638eaa7e6a3bd7d3a7324d5a7'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $res = json_decode($response);

        if (!empty($res->status) && $res->status == 'false') {
            $msg = '';
            if (!empty($res->error) && is_array($res->error)) {
                foreach ($res->error as $k => $error) {
                    $msg .= $error;
                    if (end($res->error) != $k)
                        $msg .= ',';
                }
            }
            return $result = [
                'response' => [
                    'msg'            => $msg,
                    'status'         => "failed",
                    'payment_mode'   => 'ClicknCash-api'
                ],
                'status' => 'failed'
            ];
        }

        if (!empty($res->status) && $res->status == 'true' && !empty($res->data)) {
            $data = $res->data;

            if ($data->status == 'FAILED') {
                return $result = [
                    'response' => [
                        'msg'        => $res->msg,
                        'txn_id'     => $data->txn_id,
                        'status'     => $data->status,
                        'mobile'     => $data->mobile,
                        'message'    => !empty($data->message)?$data->message:'',
                        'payment_mode' => 'ClicknCash-api'
                    ],
                    'status' => 'failed'
                ];
            }

            if ($data->status == 'PROCESSING') {
                return $result = [
                    'response' => [
                        'msg'        => $res->msg,
                        'txn_id'     => $data->txn_id,
                        'status'     => $data->status,
                        'mobile'     => $data->mobile,
                        'payment_mode' => 'ClicknCash-api'
                    ],
                   'status' => 'process'
                ];
            }

            if ($data->status == 'SUCCESS') {
                return $result = [
                    'response' => [
                        'msg'        => $res->msg,
                        'txn_id'     => $data->txn_id,
                        'status'     => 'success',
                        'mobile'     => $data->mobile,
                        'contact_id' => $data->contact_id,
                        'batch_id'   => $data->batch_id,
                        'utr_number' => $data->utr,
                        'payment_mode' => 'ClicknCash-api'
                    ],
                    'status' => 'success'
                ];
            }


        }

        return false;
    }
}
