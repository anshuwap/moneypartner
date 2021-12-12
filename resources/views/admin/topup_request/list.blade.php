@extends('admin.layouts.app')

@section('content')
@section('page_heading', 'Topup List')

<div class="row">
  <div class="col-12 mt-2">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Topup List</h3>
        <div class="card-tools">

        </div>
      </div>

      <!-- /.card-header -->
      <div class="card-body table-responsive py-4">
      <table id="table" class="table table-hover text-nowrap table-sm">
                <thead>
                  <tr>
                    <th>Sr No.</th>
                    <th>Retailer Name</th>
                    <th>Amount</th>
                    <th>Payment Mode</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @if(!empty($topup_request))
                  @php
                  $i =0;
                  @endphp
                  @foreach($topup_request as $key=>$topup)
                  <tr>
                    <td>{{ ++$i }}</td>
                    <td><a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="{{ $topup->comment }}">{{ $topup->retailer_name }}</a></td>
                    <td>{{ $topup->amount }}</td>
                    <td>{{ $topup->payment_mode }}</td>
                    <td>{{ $topup->payment_date }}</td>
                    <td id="status-{{ $topup->id }}">
                      {{ $topup->status }}
                    </td>
                    <td>
                    <div class="d-flex">
                        <a href="javascript:void(0);" class="text-success view-topup-request" topup_id="{{ $topup->id }}" data-toggle="tooltip" data-placement="bottom" title="View Details"><i class="fas fa-eye"></i></i></a>&nbsp;
                        <div class="tooltip-container">
                          <div class="tooltip-icon" topup_id="{{ $topup->id }}">
                            <i class="fas fa-sort-down"></i>
                          </div>
                          <div class="tooltip-wrapper" id="tooltip-wrapper-{{ $topup->id }}">
                            <div class="tooltip-content">
                              <form action="{{ url('admin/topup-request') }}" id="topup-request" method="post">
                                <div class="tooltip-title">
                                  <p>Place a Comment</p>
                                </div>
                                <input type="hidden" name="id" value="{{ $topup->id }}">
                                <div class="form-group">
                                  <select name="status" class="form-control control-sm" required='required'>
                                    <option value="">Select</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                  </select>
                                </div>
                                <div class="form-group">
                                  <textarea name="comment" rqueired="required" id="comment-{{ $topup->id }}" class="form-control" rows="5" placeholder="Enter Comment Here"></textarea>
                                </div>
                                <div class="form-group">
                                  <input type="submit" class="btn-sm btn btn-info" value="Submit">
                                  <a class="btn-sm btn btn-danger" id="cancel">Cancel</a>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                  @endif
                </tbody>
              </table>
      </div>
      <!-- /.card-body -->

    </div>
    <!-- /.card -->
  </div>
</div>
<!-- /.row -->
@push('custom-script')
<!-- Modal -->
<div class="modal fade" id="topup-request-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Topup Request Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="dataVal">

        </div>
        <div>
          <a href="javascript:void(0)" class="btn-sm btn-info" id="action1">Action</a>
          <div class="row" id="placeComment" style="display: none;">
            <div class="col-md-12 border mt-2">
              <form action="{{ url('admin/topup-request') }}" id="topup-request" method="post">
                <div class="tooltip-title">
                  <h6>Place a Comment</h6>
                </div>
                <input type="hidden" name="id" id="topup_id" value="">
                <div class="form-group">
                  <select name="status" class="form-control control-sm" required='required'>
                    <option value="">Select</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                  </select>
                </div>
                <div class="form-group">
                  <textarea name="comment" rqueired="required" id="comment-" class="form-control" rows="5" placeholder="Enter Comment Here"></textarea>
                </div>
                <div class="form-group">
                  <input type="submit" class="btn-sm btn btn-success" value="Submit">
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {

    $(".tooltip-icon").on("click", function() {
      var id = $(this).attr('topup_id');

      $("#tooltip-wrapper-"+id).toggleClass("active");

    });

    $("#cancel").on("click", function() {
      $(".tooltip-wrapper").toggleClass("active");
    });
    /*start form submit functionality*/
    $("form#topup-request").submit(function(e) {
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
            $('#status-' + res.id).html(res.status_msg);
            $('form#topup-request')[0].reset();
            $('.tooltip-wrapper').removeClass('active');
          }
        }
      });
    });
    /*end form submit functionality*/


    $('.view-topup-request').click(function() {
      var topup_id = $(this).attr('topup_id');
      $('#topup_id').val(topup_id);
      $.ajax({
        url: "{{ url('admin/topup-request-details') }}/" + topup_id,
        type: 'GET',
        dataType: 'JSON',
        success: function(res) {
          $('#dataVal').html(res);
          $('#topup-request-details').modal('show');
        }
      })
    })

    $('#action1').click(function() {
      $('#placeComment').toggle();
    })
  });
</script>

@endpush

@endsection