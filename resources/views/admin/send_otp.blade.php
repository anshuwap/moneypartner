<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login form</title>

    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/fontawesome-free/css/all.min.css">


    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets') }}/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('assets/custom/login.css') }}">
    <link rel="stylesheet" href="{{ asset('assets') }}/custom/custom.css">
    <style>
        .alert-success {
            color: #fff;
            background-color: #2fc296 !important;
            border-color: #2fc296 !important;
        }

        a {
            text-decoration: auto !important;
        }

        .otp {
            -moz-appearance: textfield;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<body>
    <!-- <div class="container"> -->
    <div class="login-section">
        <div class="login-form-container">
            <div class="login-form-left-side">
                <div class="form-box">
                    <div class="form-logo">
                        <img src="{{ url('assets')}}/profile/logo.png" alt="">
                    </div>
                    <div class="title-container">
                        <h2>Enter OTP</h2>
                        <span>for verify your account</span>
                    </div>

                    <!-- <div class="form-wrapper"> -->
                    <p class="login-box-msg" id="otp-msg">
                        @if (!empty($msg = Session::get('message')))
                        @if(!empty($message = $msg['msg']))
                        <?= $message ?>
                        @endif
                        @endif
                    </p>

                    <div id="timer" class="text-center mt-3"></div>
                    <div class="w-100 text-center" id="resendOpt" style="display: none;">
                        <a class="text-center send-otp" href="{{ url('resend-otp') }}">Resend OTP</a>
                    </div>

                    <form action="{{ url('verify-mobile') }}" method="post" id="otp-form">
                        {{ csrf_field() }}
                        <div class="form-group mb-3">
                            <!-- <label class="text-center">Enter OTP</label> -->
                            <div class="cover-otp d-flex ">
                                <input type="number" name="otp[]" type=" number" maxlength="1" class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="f1">
                                <input type="number" name="otp[]" type=" number" maxlength="1" class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="f2">
                                <input type="number" name="otp[]" type=" number" maxlength="1" class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="f3">
                                <input type="number" name="otp[]" type=" number" maxlength="1" class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="f4">
                            </div>
                            <span id="otp_verify" class="text-success"></span>
                            @if ($errors->has('otp'))
                            <span class="custom-text-danger">{{ $errors->first('otp') }}</span>
                            @else
                            <span id="otp_msg" class="custom-text-danger"></span>
                            @endif
                        </div>

                        <div class="row">

                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-block disabled" id="login">Verify</button>
                            </div>
                            <div class="col-md-6 mt-2" style="text-align:initial !important;">
                                <a href="{{ url('/') }}" class="text-success">Login &nbsp;<i class="fas fa-sign-in-alt"></i></a>
                            </div>
                            <div class="col-md-6 mt-2"  style="text-align:end !important;">
                                <a class="text-center send-otp" href="{{ url('resend-otp') }}" id="resendOpt1" style="display: none;">Resend OTP</a>
                            </div>
                            <!-- /.col -->
                        </div>

                    </form>

                    <!-- </div> -->
                </div>
            </div>

            <div class="login-form-right-side">
                <div class="login-content">
                    <h1>OTP Verifaction</h1>
                    <p>Please verify OTP then Access Panel.</p>
                </div>
            </div>
        </div>
    </div>
</body>

<!-- jQuery -->
<script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>

<script>

    <?php if (!empty($msg = Session::get('message'))) {

        if (!empty($otp = $msg['otp'])) {
    ?>
            var otp = "<?= $otp ?>";
            timer1(119);
        <?php } else { ?>
            $('#resendOpt1').show();
    <?php }
    } ?>

    $('#otp').keyup(function() {
        var otp = $(this).val();
        if (otp == "" || otp == null) {
            $('#otp_msg').html("Please enter OTP.");
            $('#verify').addClass('disabled');
            return false;
        } else if (otp.length < 4 || otp.length > 4) {
            $('#otp_msg').html("Please Enter 4 Digit OTP.");
            $('#verify').addClass('disabled');
            return false;
        } else {
            $('#otp_msg').html('');
            $('#verify').removeClass('disabled')
            return false;
        }
    })

    $('.otp').keypress(function() {
        if (this.value.length >= 1) {
            return false;
        }
    });

    /*start focus pointer to new field (functionality)*/
    $('#f1').keyup(function() {
        if ($('#f1').val().length == 1) {
            $('#f2').focus();
        }
    });
    $('#f2').keyup(function(e) {
        if ($('#f2').val().length == 1) {
            $('#f3').focus();
        }
         if (e.keyCode == 8) {
            $('#f1').focus();
            $('#f1').val();
        }
    });
    $('#f3').keyup(function(e) {
        if ($('#f3').val().length == 1) {
            $('#f4').focus();
        }
         if (e.keyCode == 8) {
            $('#f2').focus();
            $('#f2').val();
        }
    });
    $('#f4').keyup(function(e) {
        if ($('#f4').val().length == 1) {
            $('#login').focus();
            $("#login").removeClass("disabled");
        }
         if (e.keyCode == 8) {
            $('#f3').focus();
            $('#f3').val();
        }
    });
    /*end focus pointer to new field (functionality)*/

    /*start OTP timer functionality*/
    var timerOn = true;

    function timer1(remaining) {

        $('#resendOpt').hide();

        var m = Math.floor(remaining / 59);
        var s = remaining % 59;

        m = m < 10 ? '0' + m : m;
        s = s < 10 ? '0' + s : s;
        document.getElementById('timer').innerHTML = m + ':' + s;
        remaining -= 1;
        if (remaining >= 0) {
            setTimeout(function() {
                timer1(remaining);
            }, 1000);
            return;
        }
        // Do timeout stuff here
        // alert('Timeout for otp,Please Resend OTP');
        $('#otp-msg').html('<span class="text-danger">Timeout for OTP, Please Resend OTP.</span>');
        $('#resendOpt').show();
        $('#otp-form').hide();
        removeOtp();
    }
    /*end OTP timer fucntionality*/

    function removeOtp() {
        $.ajax({
            url: '{{ url("remove-otp") }}',
            type: 'GET',
            dataType: "json",
            success: function(res) {}
        });
    }
</script>

</html>