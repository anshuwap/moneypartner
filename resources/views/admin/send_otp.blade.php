<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Money Transfer | Send Otp</title>

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
    <div class="login-logo">
      <a href="#"><b>Money</b>Transfer</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">
          @if (!empty($msg = Session::get('message')))
          @if(!empty($message = $msg['msg']))
          <?= $message ?>
          @endif
          @endif
        </p>
<style>
  .otp{
  -moz-appearance: textfield;
}
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
        <form action="{{ url('verify-mobile') }}" method="post">
          {{ csrf_field() }}
          <div class="form-group mb-3">
            <label class="text-center">Enter OTP</label>
            <div class="cover-otp d-flex ">

              <input type="number" name="opt[]" " type=" number" maxlength="1" class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="f1">

              <input type="number" name="otp[]" " type=" number" maxlength="1" class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="f2">

              <input type="number" name="otp[]" " type=" number" maxlength="1" class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="f3">

              <input type="number" name="otp[]" " type=" number" maxlength="1" class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="f4">
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
              <button type="submit" class="btn btn-success btn-block">Verify</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        <p class="mt-3 mb-1">
          <a href="{{ url('/') }}">Login &nbsp;<i class="fas fa-sign-in-alt"></i></a>
        </p>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ asset('assets') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('assets') }}/dist/js/adminlte.min.js"></script>

  @if (!empty($msg = Session::get('message')))
  @if(!empty($otp = $msg['otp']))
  <script>
    var otp = "<?= $otp ?>";
    alert(otp);
  </script>
  @endif
  @endif

  <script>
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
    $('#f2').keyup(function() {
      if ($('#f2').val().length == 1) {
        $('#f3').focus();
      }
    });
    $('#f3').keyup(function() {
      if ($('#f3').val().length == 1) {
        $('#f4').focus();
      }
    });
    $('#f4').keyup(function() {
      if ($('#f4').val().length == 1) {
        $('#login').focus();
        $("#login").removeClass("disabled");
      }
    });
    /*end focus pointer to new field (functionality)*/
  </script>
</body>

</html>



<!--Model-->
<div class="modal fade" id="logingModal" tabindex="-1" aria-labelledby="logingModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-0 border-0">
      <div class="modal-header rounded-0 border-0">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body py-5">
        <div class="cover-logingmodal">
          <form>
            <div class="d-flex align-items-center justify-content-around w-100">
              <div class="form-row">
                <div class="form-group mb-0 w-50 col-8 col-md-8">
                  <label class="phn-number">Phone Number</label>
                  <input type="number" class="form-control rounded-0" id="phoneNo">
                  <div class="text-danger" id="phoneMsg"></div>
                </div>
                <div class="form-group otp-btn col-4 col-md-4" style="margin-top: 31px;">

                  <button class="btn btn-theme send-otp" id="send">Send OTP</button>
                </div>
              </div>
            </div>
            <div id="staus_msg" class="text-center mt-3"></div>
            <div id="timer" class="text-center mt-3"></div>
            <div id="showotp" style="display: none">
              <div class="cover-otp d-flex ">
                <input type="text" name="opt[]" maxlength='1' class="otp form-control rounded-0" id="f1">
                <input type="text" name="otp[]" maxlength='1' class="otp form-control rounded-0" id="f2">
                <input type="text" name="otp[]" maxlength='1' class="otp form-control rounded-0" id="f3">
                <input type="text" name="otp[]" maxlength='1' class="otp form-control rounded-0" id="f4">
              </div>
              <div class="d-block w-100 text-center">
                <a class="text-center send-otp" id="resendOpt" href="javascript:void(0);" style="display: none;">Resend OTP</a>
              </div>
            </div>

          </form>
        </div>
      </div>
      <div class="modal-footer otp-btn rounded-0 border-0">
        <a href="javascript:void(0);" id="login" class="btn btn-theme d-block border px-5 py-2 disabled">Login</a>
      </div>
    </div>
  </div>
</div>