@extends('retailer.layouts.app')

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
              <img class="profile-user-img img-fluid img-circle" id="avatar" src="{{ (!empty($outlet->profile_image))?asset('attachment/').'/'.$outlet->profile_image:profileImage() }} " alt="User profile picture">
            </div>

            <h3 class="profile-username text-center">{{ ucwords($outlet->retailer_name) }}</h3>

            <p class="text-muted text-center">{{ ucwords($outlet->user_type) }}</p>

            <ul class="list-group list-group-unbordered mb-3">
              <li class="list-group-item">
                <b>Total Topup Money</b> <a class="float-right">{!! mSign(Auth::user()->total_amount) !!}</a>
              </li>
              <li class="list-group-item">
                <b>Spent Topup Money</b> <a class="float-right">{!! mSign(Auth::user()->spent_amount) !!}</a>
              </li>
              <li class="list-group-item">
                <b>Available Money</b> <a class="float-right">{!! mSign(Auth::user()->available_amount) !!}</a>
              </li>
            </ul>
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
              {{ $outlet->mobile_no }}, {{ $outlet->alternate_number }}
            </p>

            <hr>

            <strong><i class="fas fa-map-marker-alt mr-1"></i>Outlet Address</strong>

            <p class="text-muted">{{ $outlet->outlet_address }}</p>

            <hr>

            <strong><i class="fas fa-map-marker-alt mr-1"></i>User Address</strong>

            <p class="text-muted">{{ $outlet->permanent_address }}</p>
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
              <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Outlet Information</a></li>
              <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Identity Information</a></li>
              <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Personal Information</a></li>
            </ul>
          </div><!-- /.card-header -->
          <div class="card-body">
            <div class="tab-content">
              <div class="active tab-pane" id="activity">
                <!-- Post -->
                <div class="post">

                  <div class="row">
                    <div class="col-md-6">
                      <table class="table table-sm">
                        <tr>
                          <th>Outlet Type :</th>
                          <td>{{ ucwords($outlet->outlet_type) }}</td>
                        </tr>
                        <tr>
                          <th>Outlet Name :</th>
                          <td>{{ ucwords($outlet->outlet_name) }}</td>
                        </tr>
                        <tr>
                          <th>Outlet Address :</th>
                          <td>{{ ucwords($outlet->outlet_address) }}</td>
                        </tr>
                        <tr>
                          <th>Outlet State :</th>
                          <td>{{ ucfirst($outlet->state) }}</td>
                        </tr>
                        <tr>
                          <th>Outlet City :</th>
                          <td>{{ ucfirst($outlet->city) }}</td>
                        </tr>
                        <tr>
                          <th>Outlet pincode :</th>
                          <td>{{ ($outlet->pincode) }}</td>
                        </tr>
                        <tr>
                          <th>Incorporation Date :</th>
                          <td>{{ $outlet->incorporation_date }}</td>
                        </tr>
                        <tr>
                          <th>Pancard Number :</th>
                          <td>{{ $outlet->pancard }}</td>
                        </tr>
                        <tr>
                          <th>Account Status :</th>
                          <td>{{ ($outlet->account_status)?"ACtive":"Inactive" }}</td>
                        </tr>

                      </table>
                    </div>
                    <div class="col-md-6">
                      <div class="row">

                        @if(!empty($outlet->office_photo))
                        <div class="col-md-12">
                          <div class="card p-2"><strong>Office Photo</strong>
                            <span>Attachment Link-<a href="{{ (!empty($outlet->office_photo))?asset('attachment/').'/'.$outlet->office_photo:'' }}" target="_blank">{{$outlet->office_photo}}</a></span>
                          </div>
                        </div>
                        @endif
                        @if(!empty($outlet->office_address_proff))
                        <div class="col-md-12">
                          <div class="card p-2"><strong>Office Address Proff</strong>
                            <span>Attachment Link- <a href="{{ (!empty($outlet->office_address_proff))?asset('attachment/').'/'.$outlet->office_address_proff:'' }}" target="_blank">{{$outlet->office_address_proff}}</a></span>
                            <!-- <img src="{{ (!empty($outlet->office_address_proff))?asset('attachment/').'/'.$outlet->office_address_proff:asset('assets').'/dist/img/user4-128x128.jpg' }}"> -->
                          </div>
                        </div>
                        @endif
                        @if(!empty($outlet->office_photo))
                        <div class="col-md-12">
                          <div class="card p-2"><strong>GST Certificate</strong>
                            <span>Attachment Link- <a href="{{ (!empty($outlet->office_photo))?asset('attachment/').'/'.$outlet->office_photo:'' }}" target="_blank">{{$outlet->office_photo}}</a></span>
                            <!-- <img src="{{ (!empty($outlet->office_photo))?asset('attachment/').'/'.$outlet->office_photo:asset('assets').'/dist/img/user4-128x128.jpg' }}"> -->
                          </div>
                        </div>
                        @endif
                      </div>
                    </div>
                  </div>

                </div>
                <!-- /.post -->
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="timeline">
                <div class="post">

                  <div class="row">
                    <div class="col-md-6">
                      <table class="table table-sm">
                        <tr>
                          <th>Date Of Birth :</th>
                          <td>{{ ucwords($outlet->date_of_birth) }}</td>
                        </tr>
                        <tr>
                          <th>Permanent Address :</th>
                          <td>{{ ucwords($outlet->permanent_address) }}</td>
                        </tr>
                        <tr>
                          <th>Id Proff :</th>
                          <td>{{ ucwords(str_replace('_',' ',$outlet->id_proff)) }}</td>
                        </tr>
                        <tr>
                          <th>Address Proff:</th>
                          <td>{{ ucfirst(str_replace('_',' ',$outlet->address_proff)) }}</td>
                        </tr>
                        <tr>
                          <th>Pancard No :</th>
                          <td>{{ ucfirst($outlet->pancard) }}</td>
                        </tr>
                      </table>
                    </div>

                    <div class="col-md-6">
                      <div class="row">
                        @if(!empty($outlet->upload_id))
                        <div class="col-md-12">
                          <div class="card p-2"><strong>{{ ucwords(str_replace('_',' ',$outlet->id_proff)) }}</strong>
                            <img src">
                            <span>Attachment Link- <a href="{{ (!empty($outlet->upload_id))?asset('attachment/').'/'.$outlet->upload_id:'' }}" target="_blank">{{$outlet->upload_id}}</a></span>
                          </div>
                        </div>
                        @endif

                        @if(!empty($outlet->upload_address))
                        <div class="col-md-12">
                          <div class="card p-2"><strong>Address Proff</strong>
                            <img src="">
                            <span>Attachment Link- <a href="{{ (!empty($outlet->upload_address))?asset('attachment/').'/'.$outlet->upload_address:'' }}" target="_blank">{{$outlet->upload_address}}</a></span>
                          </div>
                        </div>
                        @endif
                      </div>
                    </div>

                  </div>

                </div>
              </div>
              <!-- /.tab-pane -->

              <div class="tab-pane" id="settings">
                <form class="form-horizontal" id="upload-profile" action="{{ url('retailer/profile/'.$outlet->_id) }}" method="post" enctype="multipart/form-data">
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
                    <label for="inputName" class="col-sm-2 col-form-label">Retailer Name</label>
                    <div class="col-sm-10">
                      <input type="text" name="retailer_name" required value="{{ ucwords($outlet->retailer_name)}}" class="form-control" id="inputName" placeholder="Retailer Name">
                      <span id="retailer_name_msg" class="custom-text-danger"></span>
                    </div>

                  </div>

                  <div class="form-group row">
                    <label for="inputName2" class="col-sm-2 col-form-label">Mobile Number</label>
                    <div class="col-sm-10">
                      <input type="number" required name="mobile_no" class="form-control" value="{{ $outlet->mobile_no}}" id="inputName2" placeholder="Mobile Number">
                      <span id="mobile_no_msg" class="custom-text-danger"></span>
                    </div>

                  </div>
                  <div class="form-group row">
                    <label for="inputName2" class="col-sm-2 col-form-label">Alternate Number</label>
                    <div class="col-sm-10">
                      <input type="number" name="alternate_number" class="form-control" value="{{ $outlet->alternate_number }}" id="inputName2" placeholder="Alternate Number">
                      <span id="alternate_number_msg" class="custom-text-danger"></span>
                    </div>

                  </div>
                  <div class="form-group row">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                      <input type="email" name="email" readonly class="form-control" value="{{ $outlet->email }}" id="inputEmail" placeholder="Email">
                      <span id="email_msg" class="custom-text-danger"></span>
                    </div>

                  </div>

                  <div class="form-group row">
                    <label for="inputExperience" class="col-sm-2 col-form-label">Gender</label>
                    <div class="col-sm-10">
                      <select class="form-control " name="gender">
                        <option value="">Select</option>
                        <option value="male" {{ ($outlet->gender == 'male')?"selected":"" }}>Male</option>
                        <option value="female" {{ ($outlet->gender == 'female')?"selected":"" }}>Female</option>
                      </select>
                      <span id="gender_msg" class="custom-text-danger"></span>
                    </div>
                  </div>

                  <!-- <div class="form-group row">
                    <label for="" class="col-sm-2 col-form-label">Old Password</label>
                    <div class="col-sm-10">
                      <input type="password" name="old_password" class="form-control" id="oldPassword" placeholder="Old Password">
                      <span id="old_password_msg" class="custom-text-danger"></span>
                    </div>

                  </div> -->

                  <!-- <div class="form-group row">
                    <label for="" class="col-sm-2 col-form-label">New Password</label>
                    <div class="col-sm-10">
                      <input type="password" name="new_password" class="form-control" id="oldPassword" placeholder="New Password">
                      <span id="new_password_msg" class="custom-text-danger"></span>
                    </div>
                  </div> -->

                  <!-- <div class="form-group row">
                    <label for="" class="col-sm-2 col-form-label">Confirm Password</label>
                    <div class="col-sm-10">
                      <input type="password" name="confirm_password" class="form-control" id="oldPassword" placeholder="Confirm Password">
                      <span id="confirm_password_msg" class="custom-text-danger"></span>
                    </div>
                  </div> -->


                  <!-- <div class="form-group row">
                    <label for="" class="col-sm-2 col-form-label">PIN</label>
                    <div class="col-sm-10">
                      <input type="password" name="pin" class="form-control" id="pin" placeholder="Enter PIN" value="{{ Auth::user()->pin}}" style=" background: #b2d8cd;ss">
                      <span id="pin_msg" class="custom-text-danger"></span>
                    </div>
                  </div> -->

                  <div class="form-group row">
                    <div class="offset-sm-2 col-sm-10">
                      <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
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