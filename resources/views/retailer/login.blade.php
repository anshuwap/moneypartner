<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Retailer Login</title>

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

<body class="hold-transition login-page">
  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-success">
      <div class="card-header text-center">
        <a href="{{ url('/dashboard') }}" class="h1"><b>Retailer</b>Login</a>
      </div>
      <div class="card-body">

        @if ($message = Session::get('success'))
        <div class="alert alert-success">
          <p>{{ $message }}</p>
        </div>
        @endif
        @if($message = Session::get('error'))
        <div class="alert alert-danger">
          <p>{{ $message }}</p>
        </div>
        @endif

        <!-- <p class="login-box-msg">Sign in to start your session</p> -->

        <form action="{{ url('login') }}" method="post">
          @csrf
          <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
            @error('email')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
          <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
            @error('password')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
          <div class="row">
            <!-- <div class="col-12">
              <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">
                  Remember Me
                </label>
              </div>
            </div> -->
            <!-- /.col -->
            <div class="col-12">
              <button type="submit" class="btn btn-success btn-block">Sign In</button>
            </div>
            <!-- /.col -->
          </div>
        </form>


        <!-- <p class="mb-1">
          <a href="forgot-password.html">I forgot my password</a>
        </p>
        <p class="mb-0">
          <a href="{{ url('register') }}" class="text-center">Register a new membership</a>
        </p> -->
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ asset('assets') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('assets') }}/dist/js/adminlte.min.js"></script>
</body>

</html>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <div class="container">
        <div class="login-section">
            <div class="login-form-container">
                <div class="login-form-left-side">
                    <div class="form-box">
                        <div class="form-logo">
                            <img src="./assets/img/logo/logo.png" alt="">
                        </div>
                        <div class="title-container">
                            <h1><i class="fas fa-user"></i> Login</h1>
                            <span>or use your account</span>
                        </div>

                        <div class="form-wrapper">
                            <form action="#">
                                <div class="form-field">
                                    <input type="text" placeholder="Your Username">
                                </div>
                                <div class="form-field">
                                    <input type="password" placeholder="Password">
                                </div>
                                <div class="form-field form-btn-container">
                                    <button type="submit" class="form-login-btn">LOG IN</button>
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

</html>