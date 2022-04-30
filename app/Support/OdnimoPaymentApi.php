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

        if ($res->code == 'F00') {

            return $result = [
                'response' => [
                    'msg'            => $res->message,
                    'status'         => "failed",
                    'payment_mode'   => 'Odnimo - api'
                ],
                'status' => 'failed'
            ];
        }

       $validate_sender =  $this->validateSender($input->mobile_number);

       $res = json_decode($validate_sender);

        if (!empty($res->status) && $res->status == 'SUCCESS' && $res->code == 'S00') {
            $senderMobileNumber = $res->mobileNumber;
        }else{
         $new_sender = $this->addSender($input);
          $res = json_decode($new_sender);
          if (!empty($res->status) && $res->status == 'SUCCESS' && $res->code == 'S00'){
            $senderMobileNumber = $res->mobileNumber;
          }else if(!empty($res->status) && $res->code == 'F00') {
            return $result = [
                'response' => [
                    'msg'            => $res->message,
                    'status'         => "failed",
                    'payment_mode'   => 'Odnimo - api'
                ],
                'status' => 'failed'
            ];
        }}

        $curl1 = curl_init();

        $para1   = array(
            'beneId'            => !empty($benefaciry_id)?$benefaciry_id:'',
            'amount'            => $input->amount,
            'transferId'        => uniqid(6) . rand(111111, 999999),
            'senderMobileNumber'=> $senderMobileNumber,
            'transactionType'   => "IMPS"
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
                'Authorization: Basic MTE0RjAyMjk2N0U3NDY2NzFEQzZGMDFENUVDQ0ZFMzgwQjExODdDMDpNVFkwT0RJd016YzFNamN6TWpJMg=='
            ),
        ));

        $response1 = curl_exec($curl1);
        curl_close($curl1);

        $resp = json_decode($response1);

        if (!empty($resp->status) && $resp->code == 'S00') {

            return $result = [
                'response' => [
                    'utr_number'       => !empty($resp->details->utr)?$resp->details->utr:'',
                    'reference_no'     => !empty($resp->details->referenceNo)?$resp->details->referenceNo:"",
                    'beneficiaryName'  => !empty($resp->details->beneficiaryName)?$resp->details->beneficiaryName:'',
                    'bankResponseCode' => !empty($resp->details->bankResponseCode)?$resp->details->bankResponseCode:'',
                    'msg'              => !empty($resp->message)?$resp->message:'',
                    'status'           => "success",
                    'payment_mode'     => 'Odnimo - api'
                ],
                'status' => 'success'
            ];
            // print_r($result);die;
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

 if (!empty($resp->status) && $resp->code == 'P00') {
            return $result = [
                'response' => [
                    'msg'            => $resp->message,
                    'status'         => "pending",
                    'payment_mode'   => 'Odnimo - api'
                ],
                'status' => 'pending'
            ];
        }

        return array();
    }


     function getBeneficiaryId($input)
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

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://payout.merchantpe.in/Api/v2/Client/en/remittance/getBeneId',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
        "bankAccountNumber": "' . $input->account_number . '",
        "ifscCode":"' . $input->ifsc_code . '"
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic MTE0RjAyMjk2N0U3NDY2NzFEQzZGMDFENUVDQ0ZFMzgwQjExODdDMDpNVFkwT0RJd016YzFNamN6TWpJMg=='
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $res1 = json_decode($response);

        if ($res1->code == 'S00') {
            $responseData = [
                'status'        => 'SUCCESS',
                'code'          => 'S00',
                'beneficiaryId' => (!empty($res1->details->beneficiaryId)) ? $res1->details->beneficiaryId : ''
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
                    'Authorization: Basic MTE0RjAyMjk2N0U3NDY2NzFEQzZGMDFENUVDQ0ZFMzgwQjExODdDMDpNVFkwT0RJd016YzFNamN6TWpJMg=='
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $res = json_decode($response);
            // print_r($res);die;
            if (!empty($res->status) && $res->code == 'F00') {
                return json_encode($res);
            }

            if ($res->code == 'S00') {
                $responseData = [
                    'status'        => 'SUCCESS',
                    'code'          => 'S00',
                    'beneficiaryId' => (!empty($res->details->beneficiaryId)) ? $res->details->beneficiaryId : ''
                ];
                return json_encode($responseData);
            }
            return false;
        }
    }


     function addSender($input)
    {

          $post_data   = array(
            'name'              => $input->receiver_name,
            'senderMobileNumber'=> $input->mobile_number,
            'address1'          => "New Delhi",
            'city'              => 'delhi',
            'state'             => "delhi",
            'pincode'           => '110096',
        );

        $data = json_encode($post_data);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://payout.merchantpe.in/Api/v2/Client/en/remittance/AddSender',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic MTE0RjAyMjk2N0U3NDY2NzFEQzZGMDFENUVDQ0ZFMzgwQjExODdDMDpNVFkwT0RJd016YzFNamN6TWpJMg=='
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $res = json_decode($response);
            if (!empty($res->status) && $res->code == 'S00') {
                $mobileNumber = (!empty($res->details->mobileNumber)) ? $res->details->mobileNumber : '';
               $result = [
                    'status'        => 'SUCCESS',
                    'code'          => 'S00',
                    'mobileNumber'   => $mobileNumber,
            ];
            return json_encode($result);
            }

            if (!empty($res->status) && $res->code == 'F00') {
             return json_encode( $res);
        }

        return array();

    }


     function validateSender($mobile_number)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://payout.merchantpe.in/Api/v2/Client/en/remittance/ValidateSender',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
        	"senderMobileNumber":"' . $mobile_number . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic MTE0RjAyMjk2N0U3NDY2NzFEQzZGMDFENUVDQ0ZFMzgwQjExODdDMDpNVFkwT0RJd016YzFNamN6TWpJMg=='
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

           $res = json_decode($response);
            if (!empty($res->status) && $res->code == 'S00') {
                $mobileNumber = (!empty($res->details->mobileNumber)) ? $res->details->mobileNumber : '';
                 $result = [
                    'status'        => 'SUCCESS',
                    'code'          => 'S00',
                    'mobileNumber'   => $mobileNumber,
            ];
            return json_encode($result);
            }

            if (!empty($res->status) && $res->code == 'F00') {
              return json_encode($res);
        }

        return array();

    }

}
