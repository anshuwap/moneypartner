<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
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

        .captcha-login-btn {
            border: 1px solid #e57373;
            background-color: #e57373;
            color: #ffffff;
            font-size: 17px;
            font-weight: bold;
            padding: 0px 10px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            cursor: pointer;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-section">
            <div class="login-form-container">
                <div class="login-form-left-side">
                    <div class="form-box">
                        <div class="form-logo">
                            <img src="{{ url('assets')}}/profile/logo.png" alt="">
                        </div>
                        <div class="title-container">
                            <h1><i class="fas fa-user"></i> Login</h1>
                            <span>or use your account</span>
                        </div>

                        <div class="form-wrapper">
                            @if ($message = Session::get('success'))
                            <div class=" text-danger mb-2"> {!! $message !!}</div>
                            @endif
                            <form action="{{ url('login') }}" method="post">
                                @csrf

                                <div class="form-field">
                                    <input type="email" name="email" class="" placeholder="Email">
                                    @if($errors->has('email'))
                                    <span class="custom-text-danger"><strong>{{ $errors->first('email') }}</strong></span>
                                    @endif
                                </div>

                                <div class="form-field">
                                    <input type="password" name="password" class="" placeholder="Password">
                                    @if($errors->has('password'))
                                    <span class="custom-text-danger"><strong>{{ $errors->first('password') }}</strong></span>
                                    @endif
                                </div>

                                <div class="form-group row">
                                    <!-- <label for="captcha" class="col-md-4 col-form-label text-md-right">Captcha</label> -->
                                    <div class="col-md-6 captcha" style="display: flex;">
                                        <span>{!! captcha_img() !!}</span>
                                        <button type="button" class="captcha-login-btn" class="reload" id="reload">
                                            &#x21bb;
                                        </button>
                                    </div>
                                </div>

                                <div class="form-field">
                                    <div class="col-md-6">
                                        <input id="captcha" type="text" class="form-control" placeholder="Enter Captcha" name="captcha">
                                    </div>
                                    @if($errors->has('captcha'))
                                    <span class="custom-text-danger"><strong>{{ $errors->first('captcha') }}</strong></span>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="form-login-btn">LOG IN</button>
                                    </div>

                                    <div class="col-12" style="margin-top:10px;">
                                        <div class="float-right">
                                            <a href="{{ url('send-link') }}" class="text-success"><i class="fa-solid fa-lock-keyhole"></i>&nbsp;forgot password</a>
                                        </div>
                                    </div>
                                    <!-- /.col -->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="login-form-right-side">
                    <div class="login-content">
                        <h1>Welcome Back</h1>
                        <p>Please login for a awesome money service experiance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<!-- jQuery -->
<script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>
<script type="text/javascript">
    $('#reload').click(function() {
        $.ajax({
            type: 'GET',
            url: 'reload-captcha',
            success: function(data) {
                $(".captcha span").html(data.captcha);
            }
        });
    });
</script>

</html>