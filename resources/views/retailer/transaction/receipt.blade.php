<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaction Receipt</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets') }}/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/custom/custom.css">

    <style>
        table {
            font-family: IBM Plex Sans, sans-serif !important;
            font-size: 13px !important;
            letter-spacing: 0.01em;
            height: auto;
        }

        .text {
            font-family: IBM Plex Sans, sans-serif !important;
            font-size: 13px !important;
            letter-spacing: 0.01em;
            height: auto;
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="card-body">
        <div class="row mt-5">
            <div class="col-12 mt-2">
                <div class="border bg-white p-3" style="width:1000px">
                    <div class="text-center">
                        <h5>Receipt</h5>

                    </div>
                     <div class="text-right"><small><b>Transaction ID:-</b> {{ $transaction->transaction_id}}</small></div>
                    <table class="table">

                        <tr>
                            <th>#</th>
                            <th>Sender</th>
                            <th>Beneficiary</th>
                            <th>Account No</th>
                            <th>Bank</th>
                            <th>Status</th>
                            <th>UTR No</th>
                            <th>Amount</th>
                            <th>Request Date</th>
                        </tr>

                        <tr>
                            <td>1</td>
                            <td>{{ucwords($transaction->sender_name)}}</td>
                            <td>{{ucwords($transaction->receiver_name)}}</td>
                            <td>{{!empty($transaction->payment_channel['account_number'])?$transaction->payment_channel['account_number']:''}}</td>
                            <td>{{!empty($transaction->payment_channel['bank_name'])?$transaction->payment_channel['bank_name']:''}}</td>
                            <td>{{ strtoupper($transaction->status)}}</td>
                            <td>{{ !empty($transaction->utrs) ? "$transaction->utrs" : (!empty($transaction->response['utr_number']) ? $transaction->response['utr_number'] : '-') }}</td>
                            <td>{!! !empty($transaction->amount)?mSign($transaction->amount):0 !!}</td>
                          <td>{{!empty($transaction->created)?date('d-m-Y H:i',$transaction->created):''}}</td>
                        </tr>
                        <tr>
                            <th colspan="7" class="text-center">Total:</th>
                            <th colspan="2">{!! !empty($transaction->amount)?mSign($transaction->amount):0 !!}</th>
                        </tr>
                    </table>
                    <hr />
                    <div class="text-center text"> <span>Thanks for using {{ url('/') }}.<small><span class="text-danger">*</span>Agent Commistion Charge Extra</small></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('assets') }}/dist/js/adminlte.min.js"></script>
</body>

</html>