@extends('admin.layouts.app')

@section('content')
<style>
  INPUT[type=checkbox]:focus {
    outline: 1px solid rgba(0, 0, 0, 0.2);
  }

  INPUT[type=checkbox] {
    background-color: #DDD;
    border-radius: 2px;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 17px;
    height: 17px;
    cursor: pointer;
    position: relative;
    top: 5px;
  }

  INPUT[type=checkbox]:checked {
    background-color: #2fc296;
    background: #2fc296 url("data:image/gif;base64,R0lGODlhCwAKAIABAP////3cnSH5BAEKAAEALAAAAAALAAoAAAIUjH+AC73WHIsw0UCjglraO20PNhYAOw==") 3px 3px no-repeat;
  }
</style>
<div class="row">
  <div class="col-md-12"></div>
  <div class="col-12 mt-2 ml-auto mr-auto">
    <h5>Api List</h5>
    @foreach($apis as $key=>$api)
    <div class="card p-3">
      <form id="update-api" method="post">
        <div class="row">
          <input type="hidden" name="id" value="{{ $api->_id }}">
          <div class="col-md-4">
            <div><strong>Api Name:&nbsp;&nbsp;&nbsp;</strong><span>{{ ucwords($api->name) }}</span></div>
            <div><strong>Api URL:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong><span>{{ $api->api}}</span></div>
          </div>
          <div class="col-md-2">
            <div>
              <div class="input-group mb-2">
                <div class="input-group-prepend">
                  <span class="input-group-text">Sort</span>
                </div>
                <input type="number" name="sort" class="form-control" value="{{ $api->sort }}" id="sort-{{ $api->_id}}">
              </div>
            </div>
          </div>
          <div class="col-md-2">
            <input type="checkbox" class="btn-xs" name="status" id="status" data-toggle="switchbutton" data-onlabel="Enable" data-offlabel="Disabled" data-onstyle="success" data-offstyle="danger" {{ ($api->status)?'checked':'' }}>
          </div>

          <div class="col-md-1">
            <!-- @foreach($retailers as $retailer)
                        <input class="" type="checkbox" name="retailer_ids[]" value="{{ $retailer->_id }}" class="red-input" {{ (!empty($api->retailer_ids) && in_array($retailer->_id,$api->retailer_ids))?'checked':''}}>
                        <span for="" class="">{{ ucwords($retailer->full_name) }}</span>
                        @endforeach -->
            <button type="submit" class="btn btn-sm btn-danger allocate-retailer" _id="{{$api->_id}}"><i class="far fa-check-square"></i>&nbsp;Assign</button>
          </div>
          <div class="col-md-1"></div>
          <div class="col-md-1">
            <div class="float-right">
              <button type="submit" class="btn btn-sm btn-info edit"><i class="fas fa-edit"></i>&nbsp;Update</button>
              <!-- <a href="javascript:void(0);" class="btn btn-sm btn-info edit" api_id="{{ $api->_id}}"></a> -->
            </div>
          </div>

        </div>
        <div class="row">
          <div class="col-md-4"><button type="button" id="blance{{$key}}" class="btn btn-secondary btn-xs check-blance">Check Blance</button></div>
          <div class="col-md-8" id="show-blance{{$key}}"></div>
        </div>
      </form>
    </div>
    @endforeach


    <!-- /.card -->
  </div>
</div>
<!-- /.row -->

@push('modal')

<!-- Modal -->
<div class="modal fade" id="allocate_retailer_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="heading_bank">Outlet List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="cover-loader-modal d-none">
        <div class="loader-modal"></div>
      </div>

      <div class="modal-body" id="allocate_retailer">
        <form id="allocate_retailer_account" action="{{ url('admin/a-save-allocate-retailer') }}" method="post">
          @csrf
          <div class="row">
            <div class="col-md-12" id="">
              <input type="hidden" id="r_id" name="id">
              <div id="retailer1"></div>
              <div class="form-group mt-3 text-center">
                <input type="submit" class="btn btn-success btn-sm" id="submit_bank_account" value="Submit">
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).on('click', '#blance0', function(e) {
    var url = "{{ url('get-blance0') }}";
    $.ajax({
      url: url,
      method: 'get',
      dataType: "json",
      crossDomain: true,
      success: function(res) {
        $('#show-blance0').html('<b>Avaliable Blance-&nbsp;&nbsp;</b>' + res.blance);
      },
      error: function(error) {
        console.log(error)
      }
    });
  });

  $(document).on('click', '#blance1', function(e) {
    var url = "{{ url('get-blance1') }}";
    $.ajax({
      url: url,
      method: 'get',
      dataType: "json",
      crossDomain: true,
      success: function(res) {
        $('#show-blance1').html('<b>Avaliable Blance-&nbsp;&nbsp;</b>' + res.blance);
      },
      error: function(error) {
        console.log(error)
      }
    });
  });

  $(document).on('click', '#blance2', function(e) {
    var url = "{{ url('get-blance2') }}";
    alert('Balance Check service not Avaliable.');
    return false;
    $.ajax({
      url: url,
      method: 'get',
      dataType: "json",
      crossDomain: true,
      success: function(res) {
        $('#show-blance2').html('<b>Avaliable Blance-&nbsp;&nbsp;</b>' + res.blance);
      },
      error: function(error) {
        console.log(error)
      }
    });
  });


  $(document).on('click', '#blance3', function(e) {
    var url = "{{ url('get-blance2') }}";
    alert('Balance Check service not Avaliable.');
    return false;
    $.ajax({
      url: url,
      method: 'get',
      dataType: "json",
      crossDomain: true,
      success: function(res) {
        $('#show-blance2').html('<b>Avaliable Blance-&nbsp;&nbsp;</b>' + res.blance);
      },
      error: function(error) {
        console.log(error)
      }
    });
  });


  /*start form submit functionality*/
  $("form#allocate_retailer_account").submit(function(e) {
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
        $('.cover-loader-modal').removeClass('d-none');
        $('#allocate_retailer').hide();
      },
      success: function(res) {
        //hide loader
        $('.cover-loader-modal').addClass('d-none');
        $('#allocate_retailer').show();

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
          $('form#allocate_retailer_account')[0].reset();
          setTimeout(function() {
            location.reload();
          }, 1000)
        }
      }
    });
  });

  /*end form submit functionality*/

  $(document).on('click', '.allocate-retailer', function(e) {
    e.preventDefault();
    var id = $(this).attr('_id');
    var url = "{{ url('admin/a-allocate-retailer') }}";
    $.ajax({
      url: url,
      method: 'GET',
      dataType: "JSON",
      data: {
        'id': id
      },
      success: function(res) {
        console.log(res);
        $('#retailer1').html(res);
        $('#r_id').val(id);
        $('#allocate_retailer_modal').modal('show');
      },

      error: function(error) {
        console.log(error)
      }
    });
  });

  /*start form submit functionality*/
  $("form#update-api").submit(function(e) {
    e.preventDefault();
    formData = new FormData(this);
    var url = "{{ url('admin/api-list-editApi') }}";
    $.ajax({
      data: formData,
      type: "POST",
      url: url,
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false,
      beforeSend: function() {
        $('.cover-loader-modal').removeClass('d-none');
        $('#upi-id').hide();
      },
      success: function(res) {
        //hide loader
        $('.cover-loader-modal').addClass('d-none');
        $('#upi-id').show();


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
          $('form#add_payment_channel')[0].reset();
          setTimeout(function() {
            location.reload();
          }, 1000)
        }
      }
    });
  });

  /*end form submit functionality*/
</script>

@endpush
@endsection