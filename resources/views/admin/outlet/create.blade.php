@extends('admin.layouts.app')

@section('content')
@section('page_heading', 'Create Outlet')


<form action="{{ url('outlets') }}" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-3">
            <!-- Form Element sizes -->
            <div class="card card-primary">
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
                    </div>
                    <div class="form-group">
                        <label>User Type</label>
                        <select class="form-control form-control-sm" name="user_type">
                            <option value="">Select</option>
                            <option value="retailer">Retailer</option>
                            <option value="distributor">Distributor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="text" name="mobile_no" class="form-control form-control-sm" placeholder="Mobile No">
                    </div>
                    <div class="form-group">
                        <label>Alternate Number</label>
                        <input type="text" name="alternate_number" class="form-control form-control-sm" placeholder="Alternate No">
                    </div>
                    <div class="form-group">
                        <label>Retailer Name</label>
                        <input type="text" name="retailer_name" class="form-control form-control-sm" placeholder="Retailer Name">
                    </div>
                    <div class="form-group">
                        <label>Email Id</label>
                        <input type="text" name="email" class="form-control form-control-sm" placeholder="Enter Email">
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select class="form-control form-control-sm" name="gender">
                            <option value="">Select</option>
                            <option value="retailer">Male</option>
                            <option value="distributor">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Permanent Address</label>
                        <textarea class="form-control form-control-sm"  name="permanent_address" placeholder="Enter Address"></textarea>
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
                        <select class="form-control form-control-sm" name="outlet_type">
                            <option value="">Select</option>
                            <option value="retailer">Retailer</option>
                            <option value="distributor">Distributor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Outlet Name</label>
                        <input type="text" name="outlet_name" class="form-control form-control-sm" placeholder="Outlet Name">
                    </div>
                    <div class="form-group">
                        <label>Outlet Address</label>
                        <textarea class="form-control form-control-sm"  name="outlet_address" placeholder="Enter Address"></textarea>
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <select class="form-control form-control-sm" name="state">
                            <option value="">Select</option>
                            <option value="retailer">Retailer</option>
                            <option value="distributor">Distributor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control form-control-sm" placeholder="City">
                    </div>
                    <div class="form-group">
                        <label>Pincode </label>
                        <input type="number" name="pincode" class="form-control form-control-sm" placeholder="Pincode">
                    </div>
                    <div class="form-group">
                        <label>Office Photo </label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input custom-file-input-sm" id="exampleInputFile">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>
                    </div>
                    </div>
                    <div class="form-group">
                        <label>Incorporation Date</label>
                        <input type="date" name="incorporation_date" class="form-control form-control-sm" placeholder="Incorporation Date">
                    </div>

                    <div class="form-group">
                        <label>Company Pan Card No.</label>
                        <input type="text" name="company_pancard" class="form-control form-control-sm" placeholder="Company Pan Card No.">
                    </div>

                    <div class="form-group">
                        <label>GST Certificate No.</label>
                        <input type="text" name="gst_number" class="form-control form-control-sm" placeholder="GST Certificate No.">
                    </div>

                    <div class="form-group">
                        <label>Office Address Proff</label>
                        <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input custom-file-input-sm" id="exampleInputFile">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>

                    </div>
                    </div>
                    <div class="form-group">
                        <label>Account Status</label>
                        <select class="form-control form-control-sm" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
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
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control form-control-sm" placeholder="Date Of Birth">
                    </div>
                    <div class="form-group">
                        <label>Outlet Address</label>
                        <textarea class="form-control form-control-sm"  name="outlet_address" placeholder="Enter Address"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Id Proff<label>
                        <select class="form-control form-control-sm" name="state">
                            <option value="">Select</option>
                            <option value="addhar_card">Addhar Card</option>
                            <option value="pan_card">Pan Card</option>
                            <option value="driver_licence">Driver Licence</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Uploade Id</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" naeme="uploade_id" class="custom-file-input custom-file-input-sm" id="">
                        <label class="custom-file-label" for="">Choose file</label>
                      </div>
                    </div>
                    </div>

                    <div class="form-group">
                        <label>Id Proff<label>
                        <select class="form-control form-control-sm" name="state">
                            <option value="">Select</option>
                            <option value="addhar_card">Addhar Card</option>
                            <option value="pan_card">Pan Card</option>
                            <option value="driver_licence">Driver Licence</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Uploade Address</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" naeme="uploade_address" class="custom-file-input custom-file-input-sm" id="">
                        <label class="custom-file-label" for="">Choose file</label>
                      </div>
                    </div>
                    </div>

                    <div class="form-group">
                        <label>Pan Card No.</label>
                        <input type="text" name="pancard" class="form-control form-control-sm" placeholder="Company Pan Card No.">
                    </div>

                    <div class="form-group">
                        <label>GST No.</label>
                        <input type="text" name="gst_number" class="form-control form-control-sm" placeholder="GST Certificate No.">
                    </div>


                </div>
                <!-- /.card-body -->
            </div>
        </div>

    </div>
</form>

@endsection