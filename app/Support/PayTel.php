<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class PayTel
{

    private $token = null;
    private $userID = null;
    private $apikey = null;
    private $contact_id = null;
    private $requestID = null;

    function token()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://uatapi.payteltech.com/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'UserName=7065702030&grant_type=password&Password=OLMRG63R5zzh159KgWiTwVTceZ2I3t',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        // pr(curl_error($curl), 1);
        curl_close($curl);

        $res = json_decode($response);
        $token = $res->access_token;
        if ($token) {
            $this->token = $token;
            return true;
        }
        return false;
    }


    function login()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://uatapi.payteltech.com/v1/login/LoginInfo',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "UserName":"7065702030"
             }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);
        $rsponseData = $res->responseData;

        if ($rsponseData[0]) {
            $resD = $rsponseData[0];
            $this->userID = $resD->userID;
            $this->apikey = $resD->apikey;
            return true;
        }
        return false;
    }


    function addContact($request)
    {
        $request = (object)$request;
        $payout = [
            'loginKey' => $this->apikey,
            'Email' => Auth::user()->email,
            'RemiMobile' => $request->mobile_number,
            'BeneName' => $request->receiver_name,
            'Reference_id' => rand(1111, 9999),
            'Type' => 'employee'
        ];

        $payout = json_encode($payout);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://uatapi.payteltech.com/v1/payout1.0/addcontact',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payout,
            CURLOPT_HTTPHEADER => array(
                'Authorization:  Bearer ' . $this->token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);
        $rsponseData = $res->responseData;
        if ($rsponseData) {
            $resD = $rsponseData;
            $this->contact_id = $resD->id;
            $this->requestID = $res->requestID;
            return true;
        }
        return false;
    }


    function addAccount($request)
    {
        $request = (object)$request;
        $payout = [
            'loginKey' => $this->apikey,
            'contact_id' => $this->contact_id,
            'account_type' => "bank_account",
            'bank_account' => array(
                'name' => $request->bank_name,
                'ifsc' => $request->ifsc_code,
                'account_number' => $request->account_number
            ),
        ];

        $payout = json_encode($payout);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://uatapi.payteltech.com/v1/payout1.0/addaccount',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payout,
            CURLOPT_HTTPHEADER => array(
                'Authorization:  Bearer ' . $this->token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);
        if ($res) {
            $this->requestID = $res->requestID;
            return true;
        }
        return false;
    }


    function payout($request)
    {

        self::token();
        self::login();
        self::addContact($request);
        self::addAccount($request);

        $request = (object)$request;

        $payout = [
            "loginKey" => $this->apikey,
            "RequestID" => $this->requestID,
            "remiMobile" => $request->mobile_number,
            "userName" => "7065702030",
            "bene" => array(
                "beneficiaryName" => $request->receiver_name,
                "bankname" => $request->bank_name,
                "ifsc" => $request->ifsc_code,
                "accountNumebr" => $request->account_number,
                "beniMobileNo" => $request->mobile_number,
                "transactionMode" => "IMPS",
                "bAmount" => $request->amount
            )
        ];
        $payout = json_encode($payout);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://uatapi.payteltech.com/v1/payout1.0/payout',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payout,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);
        // pr($res, 1);
        $result = [];
        if (!empty($res->statusCode) && $res->statusCode === 200) {
            $result = [
                'response' => [
                    'utr_number'     => '',
                    'transaction_id' => '',
                    'msg'            => $res->reasonPhrase,
                    'payment_mode'   => 'PayTel'
                ],
                'status' => 'success'
            ];
        }
        if (!empty($res->statusCode) && $res->statusCode !== 200) {
            $result = [
                'response' => [
                    'msg'            => !empty($res->reasonPhrase) ? $res->reasonPhrase : 'not get msg',
                    'payment_mode'   => 'PayTel'

                ],
                'status' => 'failed'
            ];
        }

        if (!empty($res->message)) {
            $result = [
                'response' => [
                    'msg'            => !empty($res->message) ? $res->message : 'not get msg',
                    'payment_mode'   => 'PayTel'

                ],
                'status' => 'failed'
            ];
        }
        return $result;
    }
}
