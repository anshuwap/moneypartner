<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Link</title>
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
                            <span>send forgot url in your email</span>
                        </div>

                        <div class="form-wrapper">
                            @if ($message = Session::get('message'))
                            <div class="mb-2"> {!! $message !!}</div>
                            @endif

                            <form action="{{ url('send-link') }}" method="post">
                                @csrf
                                <div class="form-field">
                                    <input type="email" name="email" class="form-control" placeholder="Enter Email">
                                    @if($errors->has('email'))
                                    <span class="custom-text-danger"><strong>{{ $errors->first('email') }}</strong></span>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="form-login-btn">Send Link</button>
                                    </div>
                                    <!-- /.col -->
                                </div>
                            </form>
                            <p class="" style="margin-top: 10px;">
                                <a href="{{ url('/') }}">Login &nbsp;<i class="fas fa-sign-in-alt"></i></a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="login-form-right-side">
                    <div class="login-content">
                        <h1>Forgot Password</h1>
                        <p>Please click on url sent in your Email.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>