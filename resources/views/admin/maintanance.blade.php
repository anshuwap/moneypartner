<!DOCTYPE html>
<html lang="en">
<?php

use App\Models\Setting;
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Money Partner | 500</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets') }}/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/custom/custom.css">
</head>

<body class="hold-transition login-page" style="background-color:#bce6de !important;">

    <img src="{{asset('attachment/maint.png')}}">


    <?php $setting = Setting::first(); ?>
    <h4><i class="fas fa-exclamation-triangle text-danger"></i> {{ $setting->comment}}</h4>

    @if(!$setting->status)
    <a href="{{ url('/')}}" class="btn btn-success btn-sm">Login</a>
    @endif

    <div class="login-box">
        <section class="content">
            <div class="error-page">
                <div class="error-content">
                </div>
            </div>
        </section>
    </div>
</body>

<!-- jQuery -->
<script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('assets') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets') }}/dist/js/adminlte.min.js"></script>