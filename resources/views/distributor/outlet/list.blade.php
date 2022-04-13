@extends('distributor.layouts.app')

@section('content')
@section('page_heading', 'Outlet List')

<div class="row">
  <div class="col-12 mt-2">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Outlet List</h3>
        <div class="card-tools">
        <!-- <a href="javascript:void(0);" class="btn btn-sm btn-warning mr-2"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export</a> -->
          <!-- <a href="javascript:void(0);" id="import" class="btn btn-sm btn-success mr-2"><i class="fas fa-cloud-upload-alt"></i>&nbsp;Import</a> -->
          <a href="{{ url('distributor/outlets/create') }}" class="btn btn-sm btn-success mr-2"><i class="fas fa-plus-circle"></i>&nbsp;Add</a>
        </div>
      </div>

      <!-- /.card-header -->
      <div class="card-body table-responsive py-4">
        <table id="table" class="table table-hover text-nowrap table-sm">
          <thead>
            <tr>
              <th>Sr No.</th>
              <th>Outlet No./Code</th>
              <th>Name</th>
              <th>Mobile No.</th>
              <th>Outlet Name</th>
              <th>State/City</th>
              <th>Available Balance</th>
              <th>Created Date</th>
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

@push('modal')

<!-- Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Import Csv File</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Download sample lead Import(CSV) file : <a href="{{ url('admin/order-sample') }}" class="text-green">Download</a></p>
        <form id="import_form" action="{{ url('distributor/outlet-import') }}" method="post">
          @csrf

          <div class="form-row">
            <div class="form-group col-md-10">
              <div class="input-group">
                <div class="custom-file">
                  <input type="file" name="file" class="custom-file-input custom-file-input-sm" id="imgInp" accept=".csv">
                  <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                </div>
              </div>
              <span id="file_msg" class="custom-text-danger"></span>
            </div>

            <div class="form-group col-md-2">
              <input type="submit" class="btn btn-success btn-sm" id="submit_bank_charges" value="Import">
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  $('#import').click(function(e) {
    e.preventDefault();
    $('form#import_form')[0].reset();
    let url = '{{ url("distributor/outlet-import") }}';
    $('form#import_form').attr('action', url);
    $('#importModal').modal('show');
  })


  /*start form submit functionality*/
  $("form#add_bank_charges").submit(function(e) {
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
          $('form#add_bank_charges')[0].reset();
          setTimeout(function() {
            location.reload();
          }, 2000)

        }
      }
    });
  });

  /*end form submit functionality*/
</script>

@endpush

@push('custom-script')

<script type="text/javascript">
  $(document).ready(function() {

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
        url: "{{ url('distributor/outlets-ajax') }}",
        data: {}
      },
      columns: [{
          data: "sl_no"
        },
        {
          data: 'outlet_no'
        },
        {
          data: "name"
        },
        {
          data: 'mobile_no'
        },
        {
          data: 'outlet_name'
        },
        {
          data: "state"
        },
        {
          data: "available_blance"
        },
        {
          data: "created_date"
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
        targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
      }],
    });

    $(document).on('click', '.activeVer', function() {
      var id = $(this).attr('_id');
      var val = $(this).attr('val');
      $.ajax({
        'url': "{{ url('distributor/outlets-status') }}",
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
        <form id="bankModal" action="{{ url('distributor/outlet-bank') }}" method="post">
          @csrf
          <input type="hidden" name="id" id="outlet_id">
          <div class="row">
            <div class="col-md-12">
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
      url: '{{ url("distributor/outlet-bank-get") }}/' + id,
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