<?php

namespace App\Http\Controllers;

use App\Http\Email;
use App\Support\Email as SupportEmail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function show()
    {

        $data = ['email' => "agraharibablu99@gmail.com", "subject" => 'send', 'msg' => 'qorking'];
        $email = new SupportEmail();

        $email->composeEmail($data);
    }


    public function getBlance0()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://payunie.com/api/v1/getBalance',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
"key":"HsFNUzHnm2MQszwQT383c8sWS"
}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        $data = ['blance' => mSign($response[0]->Balance)];
        die(json_encode($data));
    }


    public function getBlance1()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://payunie.com/api/v1/getBalance',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
"key":"cGb25SnErgsFSyiLCAAba9m9"
}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        $data = ['blance' => mSign($response[0]->Balance)];
        die(json_encode($data));
    }


    public function getBlance2()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://payunie.com/api/v1/getBalance',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
"key":"vPxce3A8W23XTokxvBbj34Co"
}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        $data = ['blance' => mSign($response[0]->Balance)];
        die(json_encode($data));
    }



    public function emailTemplate($msg)
    {

        return '<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Simple Transactional Email</title>
    <style>
        body {
            background-color: #f6f6f6;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        table {
            border-collapse: separate;
            width: 100%;

        }

.table-b{
     border: 2px solid #2fc296;
    border-radius: 10px;
}
        table td {
            font-family: sans-serif;
            font-size: 14px;
            vertical-align: top;
        }
        .container {
            display: block;
            Margin: 0 auto !important;
            max-width: 580px;
            padding: 10px;
            width: 580px;
        }

        .content {
            box-sizing: border-box;
            display: block;
            Margin: 0 auto;
            max-width: 580px;
            padding: 10px;
        }

        .logo {
            max-width: 180px;
            margin-bottom: 10px;
        }

        .main {
            background: #fff;
            border-radius: 3px;
            width: 100%;
        }

        .wrapper {
            box-sizing: border-box;
            padding: 40px 20px;
        }
        h1,
        h2,
        h3,
        h4 {
            color: #000000;
            font-family: sans-serif;
            font-weight: 400;
            line-height: 1.4;
            margin: 0;
            Margin-bottom: 30px;
        }

        h1 {
            font-size: 35px;
            font-weight: 300;
            text-align: center;
            text-transform: capitalize;
        }

         h3 {
            font-size: 35px;
            font-weight: 300;
            text-align: center;
            text-transform: capitalize;
        }

        h4 {
            font-size: 25px;
        }


        a {
            color: #3498db;
            text-decoration: underline;
        }

        .text-center {
            text-align: center;
        }

        .otp {
            font-weight: bold;
            font-size: 30px;
        }
        .btn {
            box-sizing: border-box;
            width: 100%;
        }

        .btn>tbody>tr>td {
            padding-bottom: 15px;
        }

        .btn table {
            width: auto;
        }

        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
        }

        .btn {
            background-color: #ffffff;
            border: solid 1px #3498db;
            border-radius: 5px;
            box-sizing: border-box;
            color: #3498db;
            cursor: pointer;
            display: inline-block;
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            width: 180px;
            text-transform: capitalize;
            text-align: center;
            color: #fff;
            margin-right: 5px;
        }

        .btn-primary {
            background-color: #3498db;
        }

        @media only screen and (max-width: 620px) {
            table[class=body] h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important;
            }
        }
    </style>
</head>

<body>
    <table cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td class="container">
                <div class="content">
                    <table class="main table-b">

                        <tr>
                            <td class="wrapper">
                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <div class="logo">
                                                <img src="https://www.moneypartner.in/assets/profile/logo.png" class="logo" alt="">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                       ' . $msg . '
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>';
    }
}
