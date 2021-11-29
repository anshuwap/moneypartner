@extends('admin.layouts.app')

@section('content')
@section('page_heading', 'Outlet List')

<div class="row">
  <div class="col-12 mt-2">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Outlet List</h3>
        <div class="card-tools">
          <a href="{{ url('admin/outlets/create') }}" class="btn btn-sm btn-success mr-4"><i class="fas fa-plus-circle"></i>&nbsp;Add</a>
        </div>
      </div>

      <!-- /.card-header -->
      <div class="card-body table-responsive py-4">
        <table id="table" class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th>Sl No.</th>
              <th>Bank Name</th>
              <th>Account No.</th>
              <th>IFSC</th>
              <th>Holder Name</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
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

<script type="text/javascript">
  $(document).ready(function() {


    // function deleteRecord(id) {
    //   // var id = $(this).attr('_id');
    //   var parent = $(this).parent().parent();
    //   swal({
    //       title: "Are you sure?",
    //       text: "Once deleted, you will not be able to recover this record!",
    //       icon: "warning",
    //       buttons: true,
    //       dangerMode: true,
    //     })
    //     .then((willDelete) => {
    //       $.ajax({
    //         type: "POST",
    //         url: "" + id,
    //         data: {
    //           'id': id
    //         },
    //         dataType: 'json',
    //         success: function(res) {
    //           if (res.status == 'success') {
    //             location.reload();
    //           }
    //         }
    //       });
    //       if (willDelete) {
    //         var iconClass = 'danger';
    //         if (res.status == 'success')
    //           var iconClass = 'success';
    //         swal(`${res.msg}`, {
    //           icon: iconClass,
    //         });
    //       } else {
    //         swal("Your record is safe!");
    //       }
    //     });
    // }

    $('#table').DataTable({
      lengthMenu: [
        [10, 30, 50, 100, 500],
        [10, 30, 50, 100, 500]
      ], // page length options

      bProcessing: true,
      serverSide: true,
      scrollY: "auto",
      scrollCollapse: true,
      'ajax': {
        "dataType": "json",
        url: "{{ url('admin/outlets-ajax') }}",
        data: {}
      },
      columns: [{
          data: "sl_no"
        },
        {
          data: 'bank_name'
        },
        {
          data: "account_no"
        },
        {
          data: 'ifsc'
        },
        {
          data: 'holder_name'
        },
        {
          data: "status"
        },
        {
          data: "action"
        }
      ],

      columnDefs: [{
        orderable: false,
        targets: [0, 1, 2, 3, 4, 5, 6]
      }],
    });

    $(document).on('click', '.activeVer', function() {
      var id = $(this).attr('_id');
      var val = $(this).attr('val');
      $.ajax({
        'url': "{{ url('admin/outlets-status') }}",
        data: {
          "_token": "{{ csrf_token() }}",
          'id': id,
          'status': val
        },
        type: 'POST',
        dataType: 'json',
        success: function(res) {
          if (res.val == 1) {
            $('#active_' + id).text('Active');
            $('#active_' + id).attr('val', '0');
            $('#active_' + id).removeClass('badge-danger');
            $('#active_' + id).addClass('badge-success');
          } else {
            $('#active_' + id).text('Inactive');
            $('#active_' + id).attr('val', '1');
            $('#active_' + id).removeClass('badge-success');
            $('#active_' + id).addClass('badge-danger');
          }
          Swal.fire(
            `${res.status}!`,
            res.msg,
            `${res.status}`,
          )
        }
      })

    })

  });
</script>
@endpush

@push('modal')

<!-- Modal -->
<div class="modal fade" id="banckModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Bank Charges/Commission</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="bankModal" action="{{ url('admin/outlet-bank') }}" method="post">
          @csrf
          <input type="hidden" name="id" id="outlet_id">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Sl.</label>
                <input type="text" placeholder="Enter Sl."  id="sl" required name="sl" class="form-control form-control-sm">
                <span id="sl_msg" class="custom-text-danger"></span>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>From Amount</label>
                  <input type="number" name="from_amount" id="from_amount" required class="form-control form-control-sm" placeholder="Enter Amount">
                  <span id="from_amount_msg" class="custom-text-danger"></span>
                </div>
                <div class="form-group col-md-6">
                  <label>To Amount</label>
                  <input type="number" name="to_amount" id="to_amount" required class="form-control form-control-sm" placeholder="Enter Amount">
                  <span id="to_amount_msg" class="custom-text-danger"></span>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Type</label>
                  <select class="form-control form-control-sm" id="type" name="type">
                    <option value="persantage">Persantage(%)</option>
                    <option value="inr">INR</option>
                  </select>
                  <span id="type_msg" class="custom-text-danger"></span>
                </div>
                <div class="form-group col-md-6">
                  <label>Charges</label>
                  <input type="number" required name="charges" id="charges" class="form-control form-control-sm" placeholder="Enter Charges">
                  <span id="charges_msg" class="custom-text-danger"></span>
                </div>
              </div>

              <div class="form-group text-center">
                <input type="submit" class="btn btn-success btn-sm" value="Submit" t>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
  $(document).on('click', '.banckModal', function() {
    var id = $(this).attr('outlet_id');
    $.ajax({
      url: '{{ url("admin/outlet-bank-get") }}/' + id,
      type: "GET",
      data: {},
      dataType: "JSON",
      success: function(res) {
        $('#outlet_id').val(id);
        $('#sl').val(res.data.sl);
        $('#to_amount').val(res.data.to_amount);
        $('#from_amount').val(res.data.from_amount);
        $('#type').val(res.data.type);
        $('#charges').val(res.data.charges);
        $('#banckModal').modal('show');
      }
    })

  })

  /*start form submit functionality*/
  $("form#bankModal").submit(function(e) {
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