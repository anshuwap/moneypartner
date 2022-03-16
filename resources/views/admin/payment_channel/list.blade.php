@extends('admin.layouts.app')

@section('content')
@section('page_heading', 'Payment Channel List')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment Channel List</h3>
                <div class="card-tools">
                    <a href="javascript:void(0);" class="btn btn-sm btn-success mr-4" id="create_payment_channel"><i class="fas fa-plus-circle"></i>&nbsp;Add</a>
                </div>
            </div>

            <!-- /.card-header -->
            <div class="card-body table-responsive py-4">
                <table id="table" class="table table-sm table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Name</th>
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

        $(document).on('click', '.remove_paymnet_channel', function() {
            var id = $(this).attr('paymnet_channel_id');
            var url = "{{ url('admin/payment-channel') }}/" + id;
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
                url: "{{ url('admin/payment-channel-ajax') }}",
                data: {}
            },
            columns: [{
                    data: "sl_no"
                },
                {
                    data: 'name'
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
                targets: [0, 1, 2, 3, 4]
            }],
        });

        $(document).on('click', '.activeVer', function() {
            var id = $(this).attr('_id');
            var val = $(this).attr('val');
            $.ajax({
                'url': "{{ url('admin/payment-channel-status') }}",
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
<div class="modal fade" id="add_payment_channel_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_payment_channel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body" id="upi-id">
                <form id="add_payment_channel" action="{{ url('admin/payment-channel') }}" method="post">
                    @csrf
                    <div id="put"></div>
                    <div class="row">
                        <div class="col-md-12">

                            <div class="form-group">

                                <label>Channel Name</label>
                                <input type="text" placeholder="Enter Channel Name" id="name" required name="name" class="form-control form-control-sm">
                                <span id="name_msg" class="custom-text-danger"></span>

                            </div>

                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control form-control-sm" id="status" name="status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <span id="status_msg" class="custom-text-danger"></span>
                                </div>


                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-success btn-sm" id="submit_payment_channel" value="Submit">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $('#create_payment_channel').click(function(e) {
        e.preventDefault();
        $('form#add_payment_channel')[0].reset();
        let url = '{{ url("admin/payment-channel") }}';
        $('#heading_payment_channel').html('Add Payment Channel');
        $('#put').html('');
        $('form#add_payment_channel').attr('action', url);
        $('#submit_payment_channel').val('Submit');
        $('#add_payment_channel_modal').modal('show');
    })


    $(document).on('click', '.edit_paymnet_channel', function(e) {
        e.preventDefault();
        var id = $(this).attr('paymnet_channel_id');
        var url = "{{ url('admin/payment-channel') }}/" + id + "/edit";
        $.ajax({
            url: url,
            method: 'GET',
            dataType: "JSON",
            data: {
                id: id,
            },
            success: function(res) {
                $('#name').val(res.name);
                $('#upi_id').val(res.upi_id);
                $('#status').val(res.status);

                let urlU = '{{ url("admin/payment-channel") }}/' + id;
                $('#heading_payment_channel').html('Edit Payment Channel');
                $('#put').html('<input type="hidden" name="_method" value="PUT">');
                $('form#add_payment_channel').attr('action', urlU);
                $('#submit_payment_channel').val('Update');
                $('#add_payment_channel_modal').modal('show');
            },

            error: function(error) {
                console.log(error)
            }
        });
    });

    /*start form submit functionality*/
    $("form#add_payment_channel").submit(function(e) {
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