@extends('retailer.layouts.app')
@section('content')
@section('page_heading', 'Spent Amount Topup List')


<div class="row">

    <div class="col-md-6 mt-1">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Pin</h3>
            </div>

            <div class="card-body">
                <div class="col-sm-12">
                    <div class="position-relative p-3 bg-teal disabled color-palette">
                        <div class="ribbon-wrapper">
                            <div class="ribbon btn-success">
                                Pin
                            </div>
                        </div>
                        <input type="hidden" id="retailer_id" value="{{ Auth::user()->_id }}" />
                        <div class="form-group mr-4">
                            <label>Old Pin</label>
                            <input type="password" value="" class="form-control form-control form-control-sm" placeholder="Enter Old PIN" name="old_pin" id="old_pin">
                            <span id="old_pin_msg" class="custom-text-danger"></span>
                        </div>

                        <div class="form-group mr-4">
                            <label>New Pin</label>
                            <input type="password" value="" class="form-control form-control form-control-sm" placeholder="Enter New PIN" name="new_pin" id="new_pin">
                            <span id="new_pin_msg" class="custom-text-danger"></span>
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn btn-info btn-flat" id="change-pin">update</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Reset Pin</h3>
            </div>

            <div class="card-body">
                <div class="col-sm-12">
                    <div class="position-relative p-3 bg-teal disabled color-palette">
                        <div class="ribbon-wrapper">
                            <div class="ribbon btn-success">
                                Reset
                            </div>
                        </div>
                        <input type="hidden" id="retailer_id" value="{{ Auth::user()->_id }}" />
                        <div class="form-group mr-4">
                            <label>Email</label>
                            <input type="email" value="{{ Auth::user()->email }}" class="form-control form-control form-control-sm" placeholder="Enter Email" name="email" id="email" readonly="readonly">
                            <span id="email_msg" class="custom-text-danger"></span>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-info btn-flat" id="reset-pin">Send Link</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="col-md-6 mt-1">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Password</h3>
            </div>

            <div class="card-body">
                <div class="col-sm-12">
                    <div class="position-relative p-3 bg-teal disabled color-palette">
                        <div class="ribbon-wrapper">
                            <div class="ribbon btn-success" style="font-size:9px">
                                Password
                            </div>
                        </div>

                        <input type="hidden" id="retailer_id" value="{{ Auth::user()->_id }}" />
                        <div class="form-group mr-4">
                            <span>Old Password</span>
                            <input type="password" value="" class="form-control form-control-sm" placeholder="Enter Old Password" name="old_password" id="old_password">
                            <span id="old_password_msg" class="custom-text-danger"></span>
                        </div>

                        <div class="form-group mr-4">
                            <span>New Password</span>
                            <input type="password" value="" class="form-control form-control-sm" placeholder="Enter New Password" name="new_password" id="new_password">
                            <span id="new_password_msg" class="custom-text-danger"></span>
                        </div>

                        <div class="form-group mr-4">
                            <span>Confirm Password</span>
                            <input type="password" value="" class="form-control form-control-sm" placeholder="Confirm Password" name="confirm_password" id="confirm_password">
                            <span id="confirm_password_msg" class="custom-text-danger"></span>
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn btn-info btn-flat" id="change-password">update</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.row -->

@push('modal')
<script>
    $(document).ready(function() {

        $("#change-pin").click(function(e) {
            e.preventDefault();
            var old_pin = $('#old_pin').val();
            var new_pin = $('#new_pin').val();
            var url = "<?= url('retailer/change-pin') ?>";

            $.ajax({
                data: {
                    'old_pin': old_pin,
                    'new_pin': new_pin,
                    "_token": "{{ csrf_token() }}"
                },
                type: "POST",
                url: url,
                dataType: 'json',
                beforeSend: function() {
                    $('#change-pin').html('Processing...');
                },
                success: function(res) {
                    //hide loader
                    $('#change-pin').html('submit');

                    /*Start Validation Error Message*/
                    $('span.custom-text-danger').html('');
                    $.each(res.validation, (index, msg) => {
                        $(`#${index}_msg`).html(`${msg}`);
                    })
                    /*Start Validation Error Message*/

                    /*Start Status message*/
                    if (res.status == 'success' || res.status == 'error') {
                        Swal.fire(
                            `${res.status}!`,
                            res.msg,
                            `${res.status}`,
                        )
                    }
                    /*End Status message*/
                }
            });
        });


$("#reset-pin").click(function(e) {
            e.preventDefault();
            var email = $('#email').val();
            var url = "<?= url('retailer/send-email-link') ?>";

            $.ajax({
                data: {
                    'email': email,
                    "_token": "{{ csrf_token() }}"
                },
                type: "POST",
                url: url,
                dataType: 'json',
                beforeSend: function() {
                    $('#reset-pin').html('Processing...');
                },
                success: function(res) {
                    //hide loader
                    $('#reset-pin').html('submit');

                    /*Start Validation Error Message*/
                    $('span.custom-text-danger').html('');
                    $.each(res.validation, (index, msg) => {
                        $(`#${index}_msg`).html(`${msg}`);
                    })
                    /*Start Validation Error Message*/

                    /*Start Status message*/
                    if (res.status == 'success' || res.status == 'error') {
                        Swal.fire(
                            `${res.status}!`,
                            res.msg,
                            `${res.status}`,
                        )
                    }
                    /*End Status message*/
                }
            });
        });

        $("#change-password").click(function(e) {
            e.preventDefault();
            var new_password = $('#new_password').val();
            var old_password = $('#old_password').val();
            var confirm_password = $('#confirm_password').val();
            var url = "<?= url('retailer/change-password') ?>";

            $.ajax({
                data: {
                    'new_password': new_password,
                    'old_password': old_password,
                    'confirm_password': confirm_password,
                    "_token": "{{ csrf_token() }}"
                },
                type: "POST",
                url: url,
                dataType: 'json',
                beforeSend: function() {
                    $('#change-password').html('Processing...');
                },
                success: function(res) {
                    //hide loader
                    $('#change-password').html('update');

                    /*Start Validation Error Message*/
                    $('span.custom-text-danger').html('');
                    $.each(res.validation, (index, msg) => {
                        $(`#${index}_msg`).html(`${msg}`);
                    })
                    /*Start Validation Error Message*/


                    /*Start Status message*/
                    if (res.status == 'success' || res.status == 'error') {
                        Swal.fire(
                            `${res.status}!`,
                            res.msg,
                            `${res.status}`,
                        )
                    }
                    /*End Status message*/
                }
            });
        });

    })
</script>
@endpush

@endsection