<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
                            <h2 style="margin-bottom: 10px;">Forgot Password</h2>
                            <span>Enter new password for Reset</span>
                        </div>

                        <div class="form-wrapper">
                            @if ($message = Session::get('message'))
                            <div class="mb-2"> {!! $message !!}</div>
                            @endif

                            <form action="{{ url('forgot-password') }}" method="post">
                                @csrf
                                <input type="hidden" value="{{ $token }}" name="token">

                                <div class="form-field">
                                    <input type="password" name="password" class="form-control" placeholder="Enter Password">
                                    @if($errors->has('password'))
                                    <span class="custom-text-danger"><strong>{{ $errors->first('password') }}</strong></span>
                                    @endif
                                </div>

                                <div class="form-field">
                                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                                    @if($errors->has('confirm_password'))
                                    <span class="custom-text-danger"><strong>{{ $errors->first('confirm_password') }}</strong></span>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="form-login-btn">Change Password</button>
                                    </div>
                                    <!-- /.col -->
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                <div class="login-form-right-side">
                    <div class="login-content">
                        <h1>Forgot Password</h1>
                        <p>Enter new password for Reset.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>