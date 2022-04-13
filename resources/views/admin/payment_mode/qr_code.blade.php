@extends('admin.layouts.app')

@section('content')
@section('page_heading', 'QR Code List')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">QR Code List</h3>
                <div class="card-tools">
                    <a href="javascript:void(0);" class="btn btn-sm btn-success mr-4" id="create_qr_code"><i class="fas fa-plus-circle"></i>&nbsp;Add</a>
                </div>
            </div>

            <!-- /.card-header -->
            <div class="card-body table-responsive py-4">
                <table id="table" class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Name</th>
                            <th>QR Code</th>
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

        $(document).on('click', '.remove_qr_code', function() {
            var id = $(this).attr('qr_code_id');
            var url = "{{ url('admin/qr-code') }}/" + id;
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
                url: "{{ url('admin/qr-code-ajax') }}",
                data: {}
            },
            columns: [{
                    data: "sl_no"
                },
                {
                    data: 'name'
                },
                {
                    data: "qr_code"
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
                targets: [0, 1, 2, 3, 4, 5]
            }],
        });

        $(document).on('click', '.activeVer', function() {
            var id = $(this).attr('_id');
            var val = $(this).attr('val');
            $.ajax({
                'url': "{{ url('admin/qr-code-status') }}",
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
        <form id="allocate_retailer_account" action="{{ url('admin/q-save-allocate-retailer') }}" method="post">
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
<div class="modal fade" id="add_qr_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_qr">Add Qr Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body" id="qr-code">
                <form id="add_qr_code" action="{{ url('admin/qr-code') }}" method="post">
                    @csrf
                    <div id="put"></div>
                    <div class="row">
                        <div class="col-md-12">

                            <div class="text-center">
                                <img class="profile-user-img img-fluid" style="width: 250px;" id="avatar" src="{{ asset('assets') }}/profile/qrcode.png" alt="User profile picture">
                            </div>

                            <div class="form-group">
                                <label>QR Code</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="qr_code" class="custom-file-input custom-file-input-sm" id="qr_code">
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                </div>
                                <span id="qr_code_msg" class="custom-text-danger"></span>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>QR Code Name</label>
                                    <input type="text" placeholder="Enter QR Code Name" id="name" required name="name" class="form-control form-control-sm">
                                    <span id="name_msg" class="custom-text-danger"></span>
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
                                <input type="submit" class="btn btn-success btn-sm" id="submit_qr_code" value="Submit">
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
    var url = "{{ url('admin/q-allocate-retailer') }}";
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
    $('#create_qr_code').click(function(e) {
        e.preventDefault();
        $('form#add_qr_code')[0].reset();
        let url = '{{ url("admin/qr-code") }}';
        $('#heading_qr').html('Add QR Code');
        $('#put').html('');
        $('form#add_qr_code').attr('action', url);
        $('#submit_qr_code').val('Submit');
        $('#add_qr_modal').modal('show');
    })


    $(document).on('click', '.edit_qr_code', function(e) {
        e.preventDefault();
        var id = $(this).attr('qr_code_id');
        var url = "{{ url('admin/qr-code') }}/" + id + "/edit";
        $.ajax({
            url: url,
            method: 'GET',
            dataType: "JSON",
            data: {
                id: id,
            },
            success: function(res) {
                var url = "{{ asset('attachment/payment_mode/')}}/" + res.qr_code;
                $('#name').val(res.name);
                $('#avatar').attr('src', url);
                $('#status').val(res.status);

                let urlU = '{{ url("admin/qr-code") }}/' + id;
                $('#heading_qr').html('Edit QR Code');
                $('#put').html('<input type="hidden" name="_method" value="PUT">');
                $('form#add_qr_code').attr('action', urlU);
                $('#submit_qr_code').val('Update');
                $('#add_qr_modal').modal('show');
            },

            error: function(error) {
                console.log(error)
            }
        });
    });

    /*start form submit functionality*/
    $("form#add_qr_code").submit(function(e) {
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
                $('#qr-code').hide();
            },
            success: function(res) {
                //hide loader
                $('.cover-loader-modal').addClass('d-none');
                $('#qr-code').show();

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
                    $('form#add_qr_code')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 1000)
                }
            }
        });
    });

    /*end form submit functionality*/

    /*start single image preview*/
    $(document).on('change', '#qr_code', function() {
        var fileName = qr_code.files[0].name;
        const [file] = qr_code.files
        if (file) {
            $('#avatar').show();
            avatar.src = URL.createObjectURL(file)
        }
    });
    /*end single image preview*/
</script>

@endpush
@endsection