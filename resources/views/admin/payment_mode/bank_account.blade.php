@extends('admin.layouts.app')

@section('content')
@section('page_heading', 'Outlet List')

<div class="row">
  <div class="col-12 mt-2">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Bank List</h3>
        <div class="card-tools">
          <a href="javascript:void(0);" class="btn btn-sm btn-success mr-4" id="create_bank_account"><i class="fas fa-plus-circle"></i>&nbsp;Add</a>
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

@push('custom-script')

<script type="text/javascript">
  $(document).ready(function() {

    $(document).on('click', '.remove_bank_account', function() {
      var id = $(this).attr('bank_account_id');
      var url = "{{ url('admin/bank-account') }}/" + id;
      var tr = $(this).parent().parent();
      removeRecord(tr, url);
    })

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
        url: "{{ url('admin/bank-account-ajax') }}",
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
        targets: [0, 1, 2, 3, 4, 5, 6, 7]
      }],
    });

    $(document).on('click', '.activeVer', function() {
      var id = $(this).attr('_id');
      var val = $(this).attr('val');
      $.ajax({
        'url': "{{ url('admin/bank-account-status') }}",
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
        <form id="allocate_retailer_account" action="{{ url('admin/b-save-allocate-retailer') }}" method="post">
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


<!-- Modal -->
<div class="modal fade" id="add_bank_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="heading_bank">Add Bank Account</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="cover-loader-modal d-none">
        <div class="loader-modal"></div>
      </div>

      <div class="modal-body" id="bank-account">
        <form id="add_bank_account" action="{{ url('admin/bank-account') }}" method="post">
          @csrf
          <div id="put"></div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Bank Account Name</label>
                <input type="text" placeholder="Enter Bank Name" id="bank_name" required name="bank_name" class="form-control form-control-sm">
                <span id="bank_name_msg" class="custom-text-danger"></span>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Account Number</label>
                  <input type="number" name="account_number" id="account_number" required class="form-control form-control-sm" placeholder="Enter Account Name">
                  <span id="account_number_msg" class="custom-text-danger"></span>
                </div>
                <div class="form-group col-md-6">
                  <label>IFSC Code</label>
                  <input type="text" name="ifsc_code" id="ifsc_code" required class="form-control form-control-sm" placeholder="Enter IFSC Code">
                  <span id="ifsc_code_msg" class="custom-text-danger"></span>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Account Holder Name</label>
                  <input type="text" required name="account_holder_name" id="account_holder_name" class="form-control form-control-sm" placeholder="Enter Account Holder Name">
                  <span id="account_holder_name_msg" class="custom-text-danger"></span>
                </div>
                <div class="form-group col-md-6">
                  <label>Status</label>
                  <select class="form-control form-control-sm" id="status" name="status">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                  </select>
                  <span id="status_msg" class="custom-text-danger"></span>
                </div>

              </div>

              <div class="form-group text-center">
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
  $(document).on('click', '.allocate-retailer', function(e) {
    e.preventDefault();
    var id = $(this).attr('bank_account_id');
    var url = "{{ url('admin/b-allocate-retailer') }}";
    $.ajax({
      url: url,
      method: 'GET',
      dataType: "JSON",
      data:{'id':id},
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
</script>


<script>
  $('#create_bank_account').click(function(e) {
    e.preventDefault();
    $('form#add_bank_account')[0].reset();
    let url = '{{ url("admin/bank-account") }}';
    $('#heading_bank').html('Add Bank Account');
    $('#put').html('');
    $('form#add_bank_account').attr('action', url);
    $('#submit_bank_account').val('Submit');
    $('#add_bank_modal').modal('show');
  })


  $(document).on('click', '.edit_bank_account', function(e) {
    e.preventDefault();
    var id = $(this).attr('bank_account_id');
    var url = "{{ url('admin/bank-account') }}/" + id + "/edit";
    $.ajax({
      url: url,
      method: 'GET',
      dataType: "JSON",
      data: {
        id: id,
      },
      success: function(res) {
        $('#bank_name').val(res.bank_name);
        $('#account_number').val(res.account_number);
        $('#ifsc_code').val(res.ifsc_code);
        $('#account_holder_name').val(res.account_holder_name);
        $('#status').val(res.status);

        let urlU = '{{ url("admin/bank-account") }}/' + id;
        $('#heading-group').html('Edit Bank Account');
        $('#put').html('<input type="hidden" name="_method" value="PUT">');
        $('form#add_bank_account').attr('action', urlU);
        $('#submit_bank_account').val('Update');
        $('#add_bank_modal').modal('show');
      },

      error: function(error) {
        console.log(error)
      }
    });
  });

  /*start form submit functionality*/
  $("form#add_bank_account").submit(function(e) {
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
        $('#bank-account').hide();
      },
      success: function(res) {
        //hide loader
        $('.cover-loader-modal').addClass('d-none');
        $('#bank-account').show();

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
          $('form#add_bank_account')[0].reset();
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