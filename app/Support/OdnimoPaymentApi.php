<?php

namespace App\Support;

class OdnimoPaymentApi
{

    function AddBeneficiary($input)
    {
        $input = (object)$input;
        $post_data   = array(
            'name'              => $input->receiver_name,
            'email'             => Auth()->user()->email,
            'benefMobileNumber' => $input->mobile_number,
            'bankAccountNumber' => $input->account_number,
            'ifscCode'          => $input->ifsc_code,
            'address1'          => "New Delhi",
            'city'              => 'delhi',
            'state'             => "delhi",
            'pincode'           => '110096',
            'vpa'               => '',
            'bankName'          => $input->bank_name
        );

        $data = json_encode($post_data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://payout.merchantpe.in/Api/v2/Client/en/remittance/AddBeneficiary',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic MTE0RjAyMjk2N0U3NDY2NzFEQzZGMDFENUVDQ0ZFMzgwQjExODdDMCA6TVRZME9ESXdNemMxTWpjek1qSTI='
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);
        // $result = [];
        if (!empty($res->status) && $res->status == 'SUCCESS' && $res->code == 'S00') {
            $benefaciry_id = $res->details['beneficiaryId'];
        }

        if (!empty($res->status) && $res->code == 'F00') {
            return $result = [
                'response' => [
                    'msg'            => $res->message,
                    'status'         => "failed",
                    'payment_mode'   => 'Odnimo - api'
                ],
                'status' => 'failed'
            ];
        }

        $curl1 = curl_init();

        $para1   = array(
            'beneId'            => $benefaciry_id,
            'amount'            => $input->amount,
            'transferId'        => uniqid(6) . rand(111111, 999999),
            'senderMobileNumber'=> $input->mobile_number,
            'transactionType'   => "NEFT"
        );

        $para = json_encode($para1);

        curl_setopt_array($curl1, array(
            CURLOPT_URL => 'https://payout.merchantpe.in/Api/v2/Client/en/remittance/transaction',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $para,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic S2V5OlRva2Vu'
            ),
        ));

        $response1 = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($response1);


        if (!empty($resp->status) && $resp->code == 'S00') {
            return $result = [
                'response' => [
                    'utr_number'       => $resp->details['utr'],
                    'reference_no'     => $resp->details['referenceNo'],
                    'beneficiaryName'  => $resp->details['beneficiaryName'],
                    'bankResponseCode' => $resp->details['bankResponseCode'],
                    'msg'              => $resp->message,
                    'status'           => "success",
                    'payment_mode'     => 'Odnimo - api'
                ],
                'status' => 'success'
            ];
        }

        if (!empty($resp->status) && $resp->code == 'F00') {
            return $result = [
                'response' => [
                    'msg'            => $resp->message,
                    'status'         => "failed",
                    'payment_mode'   => 'Odnimo - api'
                ],
                'status' => 'failed'
            ];
        }

        return array();
    }
}
