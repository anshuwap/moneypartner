@extends('distributor.layouts.app')

@section('content')

<div class="row">
  <div class="col-12 mt-2">
    <div class="card">

      <div class="card-header">
        <div class="row">
          <div class="col-md-10">
            <h3 class="card-title">Topup List</h3>
          </div>
          <div class="col-md-2 d-flex">
            <div>
              @if(!empty($filter))
              <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
              @else
              <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
              @endif
              <a href="{{ url('distributor/topup-list-export') }}{{ !empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''}}" class="btn btn-sm btn-success mr-2"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export</a>
            </div>
          </div>
        </div>
      </div>

      <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
        <div class="col-md-12 ml-auto">
          <form action="{{ url('distributor/topup-list') }}">
            <div class="form-row">

              <div class="form-group col-md-2">
                <label>Start Data</label>
                <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" name="start_date" />
              </div>

              <div class="form-group col-md-2">
                <label>End Data</label>
                <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" name="end_date" id="end-date" />
              </div>

              <div class="form-group col-md-2">
                <label>Transaction Id</label>
                <input type="text" class="form-control form-control-sm" placeholder="Transaction ID" value="<?= !empty($filter['transaction_id']) ? $filter['transaction_id'] : '' ?>" name="transaction_id" id="transaction_id" />
              </div>

              <div class="form-group col-md-2">
                <label>Outlet Name</label>
                <select class="form-control-sm form-control" name="outlet_id">
                  <option value="" {{ (!empty($filter['outlet_id']) && $filter['outlet_id'] == 'all')?"selected":""}}>All</option>
                  @foreach($outlets as $outlet)
                  <option value="{{$outlet->_id}}" {{ (!empty($filter['outlet_id']) && $filter['outlet_id'] == $outlet->_id)?"selected":""}}>{{ ucwords($outlet->outlet_name)}}</option>
                  @endforeach
                </select>
              </div>

               <div class="form-group col-md-2">
                <label>Status</label>
                <select class="form-control-sm form-control" name="status">
                  <option value="" {{ (!empty($filter['status']) && $filter['status'] == 'all')?"selected":""}}>All</option>
                  <option value="success" {{ (!empty($filter['status']) && $filter['status'] == 'success')?"selected":""}}>Approved</option>\
                  <option value="rejected" {{ (!empty($filter['status']) && $filter['status'] == 'rejected')?"selected":""}}>Rejected</option>
                  <option value="pending" {{ (!empty($filter['status']) && $filter['status'] == 'pending')?"selected":""}}>Pending</option>
                </select>
              </div>

              <div class="form-group mt-4">
                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                <a href="{{ url('distributor/topup-list') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- /.card-header -->
      <div class="card-body table-responsive pl-2 pr-2">
        <table id="table" class="table table-hover text-nowrap table-sm">
          <thead>
           <tr>
              <th>Sr No.</th>
              <th>Outlet</th>
              <th>Transaction Id</th>
              <th>UTR No.</th>
              <th>Channel</th>
              <th>Amount</th>
              <th>Payment Mode</th>
              <th>Payment In</th>
              <th>Request Date</th>
              <th>Approved By</th>
              <th>Approve/Reject Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @if(!$topup_request->isEmpty())
            @php
            $i =0;
            @endphp
            @foreach($topup_request as $key=>$topup)
            <?php
            if ($topup->status == 'success') {
              $status = '<span class="tag-small">Approved</span>';
            } else if ($topup->status == 'rejected') {
              $status = '<span class="tag-small-danger">Rejected</span>';
            } else {
              $status = '<span class="tag-small-warning">Pending</span>';
            }
            $UserName = !empty($trans->UserName['full_name']) ? 'Action By- ' . $trans->UserName['full_name'] : '';
            ?>
            <tr>
              <td><span data-toggle="tooltip" data-placement="bottom" title="{{$UserName}}">{{++$key}}</span></td>
              <td><a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="{{ $topup->comment }}">{{ !empty($topup->RetailerName['outlet_name']) ? $topup->RetailerName['outlet_name'] : '' }}</a></td>
              <td><?= (!empty($topup->payment_id)) ? $topup->payment_id : '' ?></td>
              <td><?= !empty($topup->utr_no) ? $topup->utr_no : '-' ?></td>
              <td>{{ (!empty($topup->payment_channel))?ucwords($topup->payment_channel):'-' }}</td>

              <td>{!! mSign($topup->amount) !!}</td>
              <td>{{ $topup->payment_by }}</td>
              <td>{{ ucwords(str_replace('_', " ", $topup->payment_mode)) }}</td>
              <td>{{ date('d M Y H:i:s', $topup->payment_date) }}</td>
              <td>{{ !empty($topup->UserName['full_name']) ?$topup->UserName['full_name'] : '-' }}</td>
              <td>{{ !empty($topup->action_date)?date('d M Y H:i:s', $topup->action_date):'-' }}</td>
              <td id="status-{{ $topup->id }}">
                {!! $status !!}
              </td>
              <td>
                <a href="javascript:void(0);" class="text-success view-topup-request" topup_id="{{ $topup->id }}" data-toggle="tooltip" data-placement="bottom" title="View Details"><i class="fas fa-eye"></i></i></a>&nbsp;
                @if(empty($topup->admin_action) && $topup->admin_action == 0 )
                <a href="javascript:void(0);" class="text-ingfo add-topup-request" topup_id="{{ $topup->id }}" data-toggle="tooltip" data-placement="bottom" title="Approve Topup"><i class="fas fa-plus-circle"></i></a>
                @endif
              </td>
            </tr>
            @endforeach
            @endif
          </tbody>
        </table>
        {{ $topup_request->appends($_GET)->links()}}
      </div>
      <!-- /.card-body -->

    </div>
    <!-- /.card -->
  </div>
</div>
<!-- /.row -->
@push('custom-script')

<!-- Modal -->
<div class="modal fade" id="addTopup-request" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <!-- loader -->

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Place A Comment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="cover-loader-modal d-none">
        <div class="loader-modal"></div>
      </div>

      <div class="modal-body">

        <form action="{{ url('distributor/topup-request') }}" id="topup-request" method="post">

          <input type="hidden" name="id" id="topup_id" value="">
          <div class="form-group">
            <select name="status" class="form-control form-control-sm" id="status" required='required'>
              <option value="">Select</option>
              <option value="success">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>

          <div id="topup-channel"></div>

          <div class="form-group">
            <textarea name="comment" rqueired="required" id="comment-" class="form-control" rows="5" placeholder="Enter Comment Here"></textarea>
          </div>
          <div class="form-group">
            <input type="submit" class="btn-sm btn btn-success" id="submit_btn" value="Submit">
            <a class="btn-sm btn btn-danger" data-dismiss="modal" aria-label="Close" id="cancel">Cancel</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>



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
        <div id="topup-form">
          <!-- <a href="javascript:void(0)" class="btn-sm btn-info" id="action1">Action</a> -->
          <div class="row" id="placeComment" style="display: none;">
            <div class="col-md-12 border mt-2">
              <form action="{{ url('distributor/topup-request') }}" id="topup-request" method="post">
                <div class="tooltip-title">
                  <h6>Place a Comment</h6>
                </div>
                <input type="hidden" name="id" id="topup_id" value="">
                <div class="form-group">
                  <select name="status" class="form-control form-control-sm" required='required'>
                    <option value="">Select</option>
                    <option value="success">Approved</option>
                    <option value="rejected">Rejected</option>
                  </select>
                </div>
                <div class="form-group">
                  <textarea name="comment" rqueired="required" id="comment-" class="form-control" rows="4" placeholder="Enter Comment Here"></textarea>
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

    $('#status').change(function() {

      var status = $(this).val();
      if (status == 'success') {
        $('#topup-channel').html(`<div class="form-group">
                   <select name="payment_channel" class="form-control form-control-sm" id="payment_channel">
                     <option value="">Select Payment Channel</option>
                     <?php foreach ($payment_channel as $channel) {
                        echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                      } ?>
                   </select>
                   <span id="payment_channel_msg" class="custom-text-danger"></span>
                 </div>`);
      } else {
        $('#topup-channel').html(``);
      }
    })

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
          $('.cover-loader-modal').removeClass('d-none');
          $('.modal-body').hide();
        },
        success: function(res) {
          //hide loader
          $('.cover-loader-modal').addClass('d-none');
          $('.modal-body').show();

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
            $('form#topup-request')[0].reset();
            setTimeout(function() {
              location.reload();
            }, 1000)
          }
        }
      });
    });
    /*end form submit functionality*/


    $('.view-topup-request').click(function() {
      var topup_id = $(this).attr('topup_id');
      $('#topup_id').val(topup_id);
      $.ajax({
        url: "{{ url('distributor/topup-request-details') }}/" + topup_id,
        type: 'GET',
        dataType: 'JSON',
        success: function(res) {
          $('#dataVal').html(res.data);

          $('#topup-form').show();
          if (res.show_action)
            $('#topup-form').hide();

          $('#topup-request-details').modal('show');
        }
      })
    })


    $('.add-topup-request').click(function() {
      var topup_id = $(this).attr('topup_id');
      $('#topup_id').val(topup_id);
      $('#addTopup-request').modal('show');
    })

    $('#action1').click(function() {
      $('#placeComment').toggle();
    })

   //update payment channel
    $(document).on('click', '#update_payment_channel', function() {
      var id = $('#topup-id').val();
      var select = $(this);
      var payment_channel = $('#payment_channel').val();

      $.ajax({
        data: {
          'payment_channel': payment_channel,
          'id': id
        },
        type: "GET",
        url: '{{ url("distributor/topup-payment-channel") }}',
        dataType: 'json',
        beforeSend: function() {
          $(select).html('<span class="spinner-grow spinner-grow-sm" style="width: 0.75rem;height: 0.75rem;"></span>&nbsp;Loading..');
        },
        success: function(res) {
          //hide loader

          //for reset all field
          if (res.status == 'success') {
            $(select).html('<i class="fas fa-check-double"></i>&nbsp;Done');
            setTimeout(function() {
              location.reload();
            }, 1000)
          }

          /*Start Status message*/
          if (res.status == 'error') {
            $(select).html('<i class="fas fa-times"></i>&nbsp;Failed');
            Swal.fire(
              `${res.status}!`,
              res.msg,
              `${res.status}`,
            )
          }
          /*End Status message*/
        }
      });
    })

  });
</script>

@endpush

@endsection