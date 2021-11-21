@extends('admin.layouts.app')

@section('content')
@section('page_heading', 'Create Outlet')

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h5 class="m-0">Create Outlet</h5>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Create Outlet</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<form id="add-outlet" action="{{ url('admin/outlets') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-3">
            <!-- Form Element sizes -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Basic Information</h3>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label>Outlet Type</label>
                        <select class="form-control form-control-sm" name="outlet_type">
                            <option value="">Select</option>
                            <option value="retailer">Retailer</option>
                            <option value="distributor">Distributor</option>
                        </select>
                        <span id="outlet_type_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>User Type</label>
                        <select class="form-control form-control-sm" name="user_type">
                            <option value="">Select</option>
                            <option value="retailer">Retailer</option>
                            <option value="distributor">Distributor</option>
                        </select>
                        <span id="user_type_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="text" name="mobile_no" class="form-control form-control-sm" placeholder="Mobile No">
                        <span id="mobile_no_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Alternate Number</label>
                        <input type="text" name="alternate_number" class="form-control form-control-sm" placeholder="Alternate No">
                        <span id="alternate_number_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Retailer Name</label>
                        <input type="text" name="retailer_name" class="form-control form-control-sm" placeholder="Retailer Name">
                        <span id="retailer_name_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Email Id</label>
                        <input type="text" name="email" class="form-control form-control-sm" placeholder="Enter Email">
                        <span id="email_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select class="form-control form-control-sm" name="gender">
                            <option value="">Select</option>
                            <option value="retailer">Male</option>
                            <option value="distributor">Female</option>
                        </select>
                        <span id="gender_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Permanent Address</label>
                        <textarea class="form-control form-control-sm" name="permanent_address" placeholder="Enter Address"></textarea>
                        <span id="permanent_address_msg" class="text-danger"></span>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>

        <div class="col-md-3">
            <!-- Form Element sizes -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Outlet Information</h3>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label>Outlet Type</label>
                        <select class="form-control form-control-sm" name="outlet_outlet_type">
                            <option value="">Select</option>
                            <option value="retailer">Retailer</option>
                            <option value="distributor">Distributor</option>
                        </select>
                        <span id="outlet_outlet_type_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Outlet Name</label>
                        <input type="text" name="outlet_name" class="form-control form-control-sm" placeholder="Outlet Name">
                        <span id="outlet_name_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Outlet Address</label>
                        <textarea class="form-control form-control-sm" name="outlet_address" placeholder="Enter Address"></textarea>
                        <span id="outlet_address_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <select class="form-control form-control-sm" name="state">
                            <option value="">Select</option>
                            <option value="retailer">Retailer</option>
                            <option value="distributor">Distributor</option>
                        </select>
                        <span id="state_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control form-control-sm" placeholder="City">
                        <span id="city_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Pincode </label>
                        <input type="number" name="pincode" class="form-control form-control-sm" placeholder="Pincode">
                        <span id="pincode_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Office Photo </label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" naem="office_photo" class="custom-file-input custom-file-input-sm" id="exampleInputFile">
                                <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                            </div>
                        </div>
                        <span id="office_photo_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Incorporation Date</label>
                        <input type="date" name="incorporation_date" class="form-control form-control-sm" placeholder="Incorporation Date">
                        <span id="incorporation_date_msg" class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>Company Pan Card No.</label>
                        <input type="text" name="company_pancard" class="form-control form-control-sm" placeholder="Company Pan Card No.">
                        <span id="company_pancard_msg" class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>GST Certificate No.</label>
                        <input type="text" name="gst_number" class="form-control form-control-sm" placeholder="GST Certificate No.">
                        <span id="gst_number_msg" class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>Office Address Proff</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="office_address_proff" class="custom-file-input custom-file-input-sm" id="exampleInputFile">
                                <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                            </div>
                        </div>
                        <span id="office_address_proff_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Account Status</label>
                        <select class="form-control form-control-sm" name="account_status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <span id="account_status_msg" class="text-danger"></span>
                    </div>

                </div>
                <!-- /.card-body -->
            </div>
        </div>

        <div class="col-md-3">
            <!-- Form Element sizes -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Identity Information</h3>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label>User Photo </label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="user_photo" class="custom-file-input custom-file-input-sm" id="exampleInputFile">
                                <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                            </div>
                        </div>
                        <span id="user_photo_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control form-control-sm" placeholder="Date Of Birth">
                        <span id="date_of_birth_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Outlet Address</label>
                        <textarea class="form-control form-control-sm" name="outlet_address" placeholder="Enter Address"></textarea>
                        <span id="outlet_address_msg" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Id Proff</label>
                        <select class="form-control form-control-sm" name="id_proff">
                            <option value="">Select</option>
                            <option value="addhar_card">Addhar Card</option>
                            <option value="pan_card">Pan Card</option>
                            <option value="driver_licence">Driver Licence</option>
                        </select>
                        <span id="id_proff_msg" class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>Uploade Id</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" naeme="uploade_id" class="custom-file-input custom-file-input-sm" id="">
                                <label class="custom-file-label" for="">Choose file</label>
                            </div>
                        </div>
                        <span id="uploade_id_msg" class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>Address Proff</label>
                        <select class="form-control form-control-sm" name="address_proff">
                            <option value="">Select</option>
                            <option value="addhar_card">Addhar Card</option>
                            <option value="pan_card">Pan Card</option>
                            <option value="driver_licence">Driver Licence</option>
                        </select>
                        <span id="address_proff_msg" class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>Uploade Address</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" naeme="uploade_address" class="custom-file-input custom-file-input-sm" id="">
                                <label class="custom-file-label" for="">Choose file</label>
                            </div>
                        </div>
                        <span id="uploade_address_msg" class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>Pan Card No.</label>
                        <input type="text" name="pancard" class="form-control form-control-sm" placeholder="Company Pan Card No.">
                        <span id="pancard_msg" class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>GST No.</label>
                        <input type="text" name="gst_number" class="form-control form-control-sm" placeholder="GST Certificate No.">
                        <span id="gst_number_msg" class="text-danger"></span>
                    </div>


                </div>
                <!-- /.card-body -->
            </div>
        </div>

        <div class="col-md-3">
            <!-- Form Element sizes -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Option Setting</h3>
                </div>

                <div class="card-body">

                    <div class="form-group">
                        <label>Money Transfer Otion</label><br>
                        <input type="checkbox" name="money_transfer_otion['hypo']" value="1">&nbsp;&nbsp;HYPO<br>
                        <input type="checkbox" name="money_transfer_otion['offline']" value="1">&nbsp;&nbsp;Offline
                        <span id="money_transfer_otion_msg" class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>Payout Otion</label><br>
                        <input type="checkbox" name="payout_option['offile_payout']" value="1">&nbsp;&nbsp;OFFILE PAYOUT
                        <span id="payout_option_msg" class="text-danger"></span>
                    </div>

                    <div class="">
                        <input type="submit" value="Submit" class="btn btn-sm btn-success">
                    </div>
                </div>
                <!-- /.card-body -->

            </div>
        </div>

    </div>
</form>

@push('custom-script')
<script>
    /*start form submit functionality*/
    $("form#add-outlet").submit(function(e) {
        e.preventDefault();
        formData = new FormData(this);
        var url = $(this).attr('action');
        $.ajax({
            data: formData,
            type: "POST",
            url: url,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('.has-loader').addClass('has-loader-active');
            },
            success: function(res) {
                //hide loader
                $('.has-loader').removeClass('has-loader-active');
                /*Start Validation Error Message*/
                if (res.mobile_no) {
                    $('#mobileMsg').html(res.mobile_no);
                } else {
                    $('#mobileMsg').html('');
                }
                if (res.ivr_no) {
                    $('#ivrMsg').html(res.ivr_no);
                } else {
                    $('#ivrMsg').html('');
                }
                if (res.name) {
                    $('#nameMsg').html(res.name);
                } else {
                    $('#nameMsg').html('');
                }
                if (res.email) {
                    $('#emailMsg').html(res.email);
                } else {
                    $('#emailMsg').html('');
                }
                if (res.password) {
                    $('#passwordMsg').html(res.password);
                } else {
                    $('#passwordMsg').html('');
                }
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

                //for reset all field
                if (res.status == 'success') {
                    $('form#add-outlet')[0].reset();
                }
            }
        });
    });

    /*end form submit functionality*/
</script>
@endpush

@endsection