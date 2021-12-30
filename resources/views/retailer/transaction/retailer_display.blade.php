@extends('retailer.layouts.app')

@section('content')
@section('page_heading', 'Retailer List')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">
            <div class="covertabs-btn __web-inspector-hide-shortcut__">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a href="{{ url('retailer/customer-trans') }}" class="nav-link ">Customer Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('retailer/retailer-trans') }}" class="nav-link active">Retailer Transaction</a>
                    </li>
                </ul>
                <div class="add-btn">
                    <a href="javascript:void(0);" class="btn btn-sm btn-success mr-4" id="create_retailer"><i class="fas fa-plus-circle"></i>&nbsp;Add</a>
                </div>
            </div>


            <!-- /.card-header -->
            <div class="card-body table-responsive py-4 table-sm">
                <table id="table" class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Sender Name</th>
                            <th>Mobile No.</th>
                            <th>Amount</th>
                            <th>Receiver Name</th>
                            <th>Payment Mode</th>
                            <th>Status</th>
                            <th>Created Date</th>
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
                url: "{{ url('retailer/retailer-trans-ajax') }}",
                data: {}
            },
            columns: [{
                    data: "sl_no"
                },
                {
                    data: 'sender_name'
                },
                {
                    data: "mobile_number"
                },
                {
                    data: 'amount'
                },
                {
                    data: 'receiver_name'
                },
                {
                    data: "payment_mode"
                },
                {
                    data: "status",
                },
                {
                    data: "created_date"
                }
            ],

            columnDefs: [{
                orderable: false,
                targets: [0, 1, 2, 3, 4, 5, 6]
            }],
        });

    });
</script>
@endpush

@push('modal')

<!-- Modal -->
<div class="modal fade" id="retailer_trans_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_bank">Customer Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add_customer" action="{{ url('retailer/retailer-trans') }}" method="post">
                    @csrf
                    <div id="put"></div>
                    <div class="row">

                        <div class="col-md-12"><strong>Receiver Details</strong>
                            <div class="border p-3">

                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" placeholder="Enter Amount" id="amount" required name="amount" class="form-control form-control-sm">
                                    <span id="amount_msg" class="custom-text-danger"></span>
                                </div>

                                <div id="charges"></div>
                                <div id="upload_docs"></div>

                                <div class="form-group">
                                    <label>Receiver Name</label>
                                    <input type="text" placeholder="Enter Receiver Name" id="receiver" required name="receiver_name" class="form-control form-control-sm">
                                    <span id="receiver_msg" class="custom-text-danger"></span>
                                </div>

                                <div class="form-group">
                                    <label>Select Payment Channel</label>
                                    <select name="payment_mode" class="form-control form-control-sm" id="payment_channel">
                                        <option value="">Select</option>
                                        <option value="bank_account">Bank Account</option>
                                        <option value="upi">UPI</option>
                                    </select>
                                    <span id="receiver_msg" class="custom-text-danger"></span>
                                </div>

                                <div id="payment_channel_field">

                                </div>
                            </div>

                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-success btn-sm" id="submit_customer" value="Submit">
                                <!-- <input type="submit" class="btn btn-info btn-sm" value="Send"> -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $('#payment_channel').change(function() {
        var payment_channel = $(this).val();
        if (payment_channel == 'bank_account') {
            $('#payment_channel_field').html(`<div class="form-group">
            <label>Bank Name</label>
            <input type="text" name="payment_channel['bank_name']" class="form-control form-control-sm" placeholder="Enter Bank Account Name">
            </div>
            <div class="form-group">
            <label>Account Number</label>
            <input type="number" name="payment_channel['account_number']" class="form-control form-control-sm" placeholder="Enter Account Number">
            </div>
            <div class="form-group">
            <label>IFSC Code</label>
            <input type="text" name="payment_channel['ifsc_code']" class="form-control form-control-sm" placeholder="Enter IFSC Code">
            </div>`);
        } else if (payment_channel == 'upi') {
            $('#payment_channel_field').html(`<div class="form-group">
            <label>UPI Id</label>
            <input type="text" name="payment_channel['upi_id']" class="form-control form-control-sm" placeholder="Enter UPI ID">
            </div>`);
        } else {
            $('#payment_channel_field').html(``);
        }
    })



    $('#amount').keyup(function(e) {
        e.preventDefault();

        var amount = $(this).val();
        transactionFeeDetails(amount);

        if (amount >= 25000 && amount < 200000) {
            $('#upload_docs').html(`<div class="form-group">
            <label>Pancard Number</label>
            <input type="text" name="pancard_no" class=" form-control form-control-sm" placeholder="Enter Pancard Number" required>
            </div>
            <div class="form-group">
            <label>Uploade Pancard</label>
            <div class="input-group">
            <div class="custom-file">
            <input type="file" name="pancard" class=" custom-file-input custom-file-input-sm" id="attachment" required>
            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
            </div>
            </div>
            <span id="attachment_msg" class="custom-text-danger"></span>
            </div>`);
        } else if (amount > 200000) {
            $('#add_customer input,select').attr('disabled', 'disabled');
            $('#amount_msg').html('Alowed only 2 lakh Per Month.');
            $('#amount').removeAttr('disabled');
        } else {
            $('#upload_docs').html(``);
            $('#amount_msg').html(``);
            $('#add_customer input,select').removeAttr('disabled');
        }
    })


    //for check retailer fee detials
    function transactionFeeDetails(amount) {

        var amount = (amount) ? amount : 0;
        $.ajax({
            url: "<?= url('retailer/fee-details') ?>",
            data: {
                'amount': amount
            },
            dataType: "JSON",
            type: "GET",
            success: function(res) {

                if (res.status == 'success') {
                    $('#charges').html(`Rs. ${res.charges} Transaction Fees
        <div class="form-group">
        <label>Transaction Amount</label>
        <input type="text" readonly name="" value="${parseInt(amount)+ parseInt(res.charges)}" class="form-control form-control-sm">
        </div>`);

                } else if (res.status == 'error') {
                    $('#charges').html(res.msg);
                }
            }
        })
    }


    $('#create_retailer').click(function(e) {
        e.preventDefault();
        $('form#add_customer')[0].reset();
        let url = '{{ url("retailer/retailer-trans") }}';
        $('#heading_bank').html('Transaction Details');
        $('#put').html('');
        $('form#add_customer').attr('action', url);
        $('#submit_customer').val('Submit');
        $('#retailer_trans_modal').modal('show');
    })


    /*start form submit functionality*/
    $("form#add_customer").submit(function(e) {
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
                    $('form#add_customer')[0].reset();
                    location.reload();
                }
            }
        });
    });

    /*end form submit functionality*/
</script>

@endpush
@endsection