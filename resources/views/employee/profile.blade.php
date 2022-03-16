@extends('employee.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Profile</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">User Profile</li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3">

        <!-- Profile Image -->
        <div class="card card-success card-outline">
          <div class="card-body box-profile">
            <div class="text-center">
              <img class="profile-user-img img-fluid img-circle" id="avatar" src="{{ (!empty($user->employee_img))?asset('attachment/').'/'.$user->employee_img:employeeImage() }} " alt="User profile picture">
            </div>

            <h3 class="profile-username text-center">{{ ucwords($user->full_name) }}</h3>

            <p class="text-muted text-center">{{ ucwords($user->user_type) }}</p>

            <!-- <ul class="list-group list-group-unbordered mb-3">
              <li class="list-group-item">
                <b>Total Topup Money</b> <a class="float-right">{!! mSign(Auth::user()->total_amount) !!}</a>
              </li>
              <li class="list-group-item">
                <b>Spent Topup Money</b> <a class="float-right">{!! mSign(Auth::user()->spent_amount) !!}</a>
              </li>
              <li class="list-group-item">
                <b>Available Money</b> <a class="float-right">{!! mSign(Auth::user()->available_amount) !!}</a>
              </li>
            </ul> -->
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- About Me Box -->
        <div class="card card-success">
          <div class="card-header">
            <h3 class="card-title">About Me</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <strong><i class="fas fa-book mr-1"></i> Contact Number</strong>

            <p class="text-muted">
              {{ $user->mobile_number }}
            </p>

            <hr>

            <strong><i class="fas fa-map-marker-alt mr-1"></i>User Address</strong>

            <p class="text-muted">{{ $user->address }}</p>

            <hr>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
      <div class="col-md-9">
        <div class="card">
          <div class="card-header p-2">
            <ul class="nav nav-pills">

              <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Personal Information</a></li>
            </ul>
          </div><!-- /.card-header -->
          <div class="card-body">
            <div class="tab-content">

                <div class="active tab-pane" id="settings">
                  <form class="form-horizontal" id="upload-profile" action="{{ url('employee/e-profile/'.$user->_id) }}" method="post" enctype="multipart/form-data">
                    {{ method_field('PUT') }}
                    @csrf
                    <div class="form-group row">
                      <label class="col-sm-2 col-form-label">Profile Image</label>
                      <div class="col-sm-10">
                        <div class="input-group">
                          <div class="custom-file">
                            <input type="file" name="profile_image" class="custom-file-input custom-file-input-sm" id="imgInp" accept="image/png, image/gif, image/jpeg">
                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                          </div>
                        </div>

                        <span id="profile_image_msg" class="custom-text-danger"></span>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label for="inputName" class="col-sm-2 col-form-label">Full Name</label>
                      <div class="col-sm-10">
                        <input type="text" name="full_name" required value="{{ ucwords($user->full_name)}}" class="form-control" id="inputName" placeholder="Full Name">
                        <span id="full_name_msg" class="custom-text-danger"></span>
                      </div>

                    </div>

                    <div class="form-group row">
                      <label for="inputName2" class="col-sm-2 col-form-label">Mobile Number</label>
                      <div class="col-sm-10">
                        <input type="number" required name="mobile_no" class="form-control" value="{{ $user->mobile_number}}" id="inputName2" placeholder="Mobile Number">
                        <span id="mobile_no_msg" class="custom-text-danger"></span>
                      </div>

                    </div>

                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                      <div class="col-sm-10">
                        <input type="email" name="email" readonly class="form-control" value="{{ $user->email }}" id="inputEmail" placeholder="Email">
                        <span id="email_msg" class="custom-text-danger"></span>
                      </div>

                    </div>

                    <div class="form-group row">
                      <label for="inputExperience" class="col-sm-2 col-form-label">Gender</label>
                      <div class="col-sm-10">
                        <select class="form-control " name="gender">
                          <option value="">Select</option>
                          <option value="male" {{ ($user->gender == 'male')?"selected":"" }}>Male</option>
                          <option value="female" {{ ($user->gender == 'female')?"selected":"" }}>Female</option>
                        </select>
                        <span id="gender_msg" class="custom-text-danger"></span>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label for="" class="col-sm-2 col-form-label">Old Password</label>
                      <div class="col-sm-10">
                        <input type="password" name="old_password" class="form-control" id="oldPassword" placeholder="Old Password">
                        <span id="old_password_msg" class="custom-text-danger"></span>
                      </div>

                    </div>

                    <div class="form-group row">
                      <label for="" class="col-sm-2 col-form-label">New Password</label>
                      <div class="col-sm-10">
                        <input type="password" name="new_password" class="form-control" id="oldPassword" placeholder="New Password">
                        <span id="new_password_msg" class="custom-text-danger"></span>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label for="" class="col-sm-2 col-form-label">Confirm Password</label>
                      <div class="col-sm-10">
                        <input type="password" name="confirm_password" class="form-control" id="oldPassword" placeholder="Confirm Password">
                        <span id="confirm_password_msg" class="custom-text-danger"></span>
                      </div>
                    </div>


                    <div class="form-group row">
                      <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-success">Submit</button>
                      </div>
                    </div>
                  </form>
                </div>
                <!-- /.tab-pane -->

            </div><!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@push('custom-script')
<script>
  /*start form submit functionality*/
  $("form#upload-profile").submit(function(e) {
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