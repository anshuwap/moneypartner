@extends('retailer.layouts.app')
@section('content')


<div class="row">

    <div class="col-md-4 mt-1 ml-auto mr-auto">
         <div class="card card-outline card-success">
            <div class="card-header text-center">
                <h3><b>Money</b>&nbsp;Partner</h3>
            </div>

            <div class="card-body">
                 @if ($message = Session::get('message'))
                <div class="alert alert-danger">
                    <span>{{ $message }}</span>
                </div>
                @endif

                <p class="login-box-msg">Enter New Pin For Reset</p>

                <form action="{{ url('retailer/forgot-pin') }}" method="post">
                    @csrf
                    <input type="hidden" value="{{ $token }}" name="token">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="password" name="pin" class="form-control" placeholder="Enter Pin">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        @if($errors->has('pin'))
                        <span class="custom-text-danger"><strong>{{ $errors->first('pin') }}</strong></span>
                        @endif
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <input type="password" name="confirm_pin" class="form-control" placeholder="Confirm Pin">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        @if($errors->has('confirm_pin'))
                        <span class="custom-text-danger"><strong>{{ $errors->first('confirm_pin') }}</strong></span>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-block">Change Pin</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

            </div>

            <!-- /.card-body -->

        </div>
    </div>

</div>
<!-- /.row -->

@endsection