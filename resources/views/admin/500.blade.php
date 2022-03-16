<!DOCTYPE html>
<html lang="en">

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

<body class="hold-transition login-page">
    <div class="login-box">

        <!-- Main content -->
        <section class="content">
            <div class="error-page">
                <h2 class="headline text-danger">500</h2>

                <div class="error-content">
                    <h3><i class="fas fa-exclamation-triangle text-danger"></i> Oops! Something went wrong.</h3>

                    <p>
                        @if ($message = Session::get('error'))
                        {{ $message }}
                        @else
                        <script type="text/javascript">
                           window.history.back();
                        </script>

                        @endif
                    </p>
                    <button type="button" class="btn btn-success btn-sm" onclick="javascript:history.go(-1)">Back</button>

                </div>
            </div>
            <!-- /.error-page -->

        </section>
        <!-- /.content -->
    </div>
    </div>
</body>

<!-- jQuery -->
<script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('assets') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets') }}/dist/js/adminlte.min.js"></script>