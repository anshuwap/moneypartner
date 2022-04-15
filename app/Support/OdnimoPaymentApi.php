<?php

namespace App\Support;

use App\Models\OdnimoBeneficiaryModal;

class OdnimoPaymentApi
{

    function AddBeneficiary($input)
    {
        $input = (object)$input;

        $response = $this->getBeneficiaryId($input);

        $res = json_decode($response);
        // $result = [];
        if (!empty($res->status) && $res->status == 'SUCCESS' && $res->code == 'S00') {
            $benefaciry_id = $res->beneficiaryId;
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
            'senderMobileNumber' => $input->mobile_number,
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
                'Authorization: Basic MTE0RjAyMjk2N0U3NDY2NzFEQzZGMDFENUVDQ0ZFMzgwQjExODdDMCA6TVRZME9ESXdNemMxTWpjek1qSTI='
            ),
        ));

        $response1 = curl_exec($curl1);
        curl_close($curl1);
        $resp = json_decode($response1);

// print_r($resp);die;
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


    private function getBeneficiaryId($input)
    {

        //  $input = (object)$input;
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

        $benefaciry = OdnimoBeneficiaryModal::where('email', Auth()->user()->email)->where('benefMobileNumber', $input->mobile_number)
            ->where('bankAccountNumber', $input->account_number)->where('ifscCode', $input->ifsc_code)
            ->where('bankName', $input->bank_name)->first();

        if (!empty($benefaciry)) {

            $responseData = [
                'status' => 'SUCCESS',
                'code' => 'S00',
                'beneficiaryId' => $benefaciry->beneficiaryId

            ];
            return json_encode($responseData);
        } else {

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

        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        // CURLOPT_URL => 'https://payout.merchantpe.in/Api/v2/Client/en/remittance/getBeneId',
        // CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => '',
        // CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 0,
        // CURLOPT_FOLLOWLOCATION => true,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // CURLOPT_CUSTOMREQUEST => 'POST',
        // CURLOPT_POSTFIELDS =>'{
        // "bankAccountNumber": "'.$input->account_number.'",
        // "ifscCode":"'.$input->ifsc_code.'"
        // }',
        // CURLOPT_HTTPHEADER => array(
        //     'Content-Type: application/json',
        //     'Authorization: Basic MTE0RjAyMjk2N0U3NDY2NzFEQzZGMDFENUVDQ0ZFMzgwQjExODdDMCA6TVRZME9ESXdNemMxTWpjek1qSTI='
        // ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);

            $res = json_decode($response);
            if (!empty($res->status) && $res->code == 'F00') {
                return $response;
            }

            if ($res->code == 'S00') {
                $benefaciry = new OdnimoBeneficiaryModal();
                $benefaciry->name              = $input->receiver_name;
                $benefaciry->email             = Auth()->user()->email;
                $benefaciry->benefMobileNumber = $input->mobile_number;
                $benefaciry->bankAccountNumber = $input->account_number;
                $benefaciry->ifscCode          = $input->ifsc_code;
                $benefaciry->address1          =  "New Delhi";
                $benefaciry->city              = "delhi";
                $benefaciry->state             = "delhi";
                $benefaciry->pincode           = "110096";
                $benefaciry->vpa               = '';
                $benefaciry->bankName          = $input->bank_name;
                $benefaciry->beneficiaryId     = (!empty($res->details->beneficiaryId)) ? $res->details->beneficiaryId : '';

                if ($benefaciry->save())
                    $_id = $benefaciry->_id;

                $benefaciry = OdnimoBeneficiaryModal::find($_id);
                $responseData = [
                    'status'        => 'SUCCESS',
                    'code'          => 'S00',
                    'beneficiaryId' => $benefaciry->beneficiaryId
                ];
                return json_encode($responseData);
            }
             return false;
        }
    }
}
