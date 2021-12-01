@extends('retailer.layouts.app')

@section('content')
@section('page_heading', 'Topup History')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Topup History</h3>
                <div class="card-tools">
                    <a href="javascript:void(0);" class="btn btn-sm btn-success mr-2" id="create_topup"><i class="fas fa-hand-holding-usd"></i>&nbsp;Request for Topup</a>
                    <a href="{{ url('retailer/topup') }}" class="btn btn-sm btn-warning mr-4" id=""><i class="fas fa-arrow-alt-circle-left"></i>&nbsp;Back</a>
                </div>
            </div>

            <!-- /.card-header -->
            <div class="card-body table-responsive py-4">
                <table id="table" class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Name</th>
                            <th>topup Id</th>
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

        $(document).on('click', '.remove_topup_id', function() {
            var id = $(this).attr('topup_id_id');
            var url = "{{ url('admin/topup') }}/" + id;
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
                url: "{{ url('retailer/topup-ajax') }}",
                data: {}
            },
            columns: [{
                    data: "sl_no"
                },
                {
                    data: 'name'
                },
                {
                    data: "topup_id"
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
                'url': "{{ url('retailer/topup-status') }}",
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
<div class="modal fade" id="add_topup_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_totup">Request For Topup</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add_topup_id" action="{{ url('retailer/topup') }}" method="post">
                    @csrf
                    <div id="put"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Payment Mode</label>
                                <select class="form-control form-control-sm" required id="payment_mode" name="payment_mode">
                                <option value="">Select</option>
                                    <option value="bank_account">Bank Account</option>
                                    <option value="upi_id">UPI ID</option>
                                    <option value="qr_code">QR Code</option>
                                </select>
                                <span id="payment_mode_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label>Select</label>
                                <select class="form-control form-control-sm" required disabled id="payment_reference" name="payment_reference_id">
                            <option>Select</option>
                            </select>
                                <span id="payment_reference_id_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="" id="show-paymnet-details">

                            </div>

                            <div class="form-group">
                                <label>Amount</label>
                                <input type="number" placeholder="Enter Amount" id="name" required name="amount" class="form-control form-control-sm">
                                <span id="amount_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label>Comment</label>
                                <textarea class="form-control" name="comment" id="comment" rows="5"></textarea>
                                <span id="comment_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label>Uploade Screenshot</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="attachment" class="custom-file-input custom-file-input-sm" id="attachment">
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                </div>
                                <span id="attachment_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-success btn-sm" id="submit_topup_id" value="Submit">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $('#payment_mode').change(function() {
        var payment_mode = $(this).val();
        $.ajax({
            url: "{{ url('retailer/outlet-payment-mode') }}",
            data: {
                'payment_mode': payment_mode
            },
            type: 'GET',
            dataType: 'JSON',
            success: function(res) {
               $('#payment_reference').removeAttr('disabled');
                $('#payment_reference').html(res);
            }
        })
    });

    $('#payment_reference').change(function() {
        var payment_mode = $('#payment_mode').val();
        var payment_id = $(this).val();
        $.ajax({
            url: "{{ url('retailer/payment-details') }}",
            data: {
                'payment_mode': payment_mode,
                'payment_id': payment_id
            },
            type: 'GET',
            dataType: 'JSON',
            success: function(res) {
                console.log(res);
                $('#show-paymnet-details').html(res);
            }
        })
    });


    $('#create_topup').click(function(e) {
        e.preventDefault();
        $('form#add_topup_id')[0].reset();
        let url = '{{ url("retailer/topup") }}';
        $('#heading_topup').html('Request For Topup');
        $('#put').html('');
        $('form#add_topup_id').attr('action', url);
        $('#submit_topup_id').val('Submit');
        $('#add_topup_modal').modal('show');
    })


    $(document).on('click', '.edit_topup_id', function(e) {
        e.preventDefault();
        var id = $(this).attr('topup_id_id');
        var url = "{{ url('retailer/topup') }}/" + id + "/edit";
        $.ajax({
            url: url,
            method: 'GET',
            dataType: "JSON",
            data: {
                id: id,
            },
            success: function(res) {
                // var url = "{{ asset('attachment/payment_mode/')}}/" + res.topup_id;
                $('#name').val(res.name);
                $('#topup_id').val(res.topup_id);
                $('#status').val(res.status);

                let urlU = '{{ url("retailer/topup") }}/' + id;
                $('#heading_topup').html('Edit topup ID');
                $('#put').html('<input type="hidden" name="_method" value="PUT">');
                $('form#add_topup_id').attr('action', urlU);
                $('#submit_topup_id').val('Update');
                $('#add_topup_modal').modal('show');
            },

            error: function(error) {
                console.log(error)
            }
        });
    });

    /*start form submit functionality*/
    $("form#add_topup_id").submit(function(e) {
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
                    $('form#add_topup_id')[0].reset();
                }
            }
        });
    });

    /*end form submit functionality*/
</script>

@endpush
@endsection