@extends('retailer.layouts.app')

@section('content')
@section('page_heading', 'Retailer List')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">
            <div class="">
                <ul class="nav nav-tabs" role="tablist">
                   @if(!empty(moneyTransferOption()->dmt_transfer_offline))
                    <li class="nav-item">
                        <a href="{{ url('retailer/customer-trans') }}" class="nav-link "><i class="fas fa-file-invoice-dollar"></i>&nbsp;DMT Transaction</a>
                    </li>
                    @endif

                    @if(!empty(moneyTransferOption()->payout_offline))
                    <li class="nav-item">
                        <a href="{{ url('retailer/retailer-trans') }}" class="nav-link active">  <i class="fas fa-money-check nav-icon"></i>&nbsp;Payout Transaction</a>
                    </li>
                    @endif

                    @if(!empty(moneyTransferOption()->payout_offline_api))
                    <li class="nav-item">
                        <a href="{{ url('retailer/offline-payout') }}" class="nav-link"><i class="fas fa-hand-holding-usd"></i> &nbsp;Payout Api</a>
                    </li>
                    @endif
                </ul>
                <div class="add-btn">

                    <a href="javascript:void(0);" class="btn btn-sm btn-success mr-2" id="create_retailer"><i class="fas fa-plus-circle"></i>&nbsp;Add Payout</a>
                    <a href="javascript:void(0);" id="import" class="btn btn-sm btn-info mr-2"><i class="fas fa-cloud-upload-alt"></i>&nbsp;Import</a>
                </div>
            </div>


            <!-- /.card-header -->
            <div class="card-body table-responsive py-4 table-sm">
                <table id="table" class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Transaction Id</th>
                            <th>Amount</th>
                            <th>Beneficiary Name</th>
                            <th>Payment Mode</th>
                            <th>IFSC</th>
                            <th>Account No./UPI Id</th>
                            <th>Bank Name</th>
                            <th>Status</th>
                            <th>Datetime</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($retailerTrans as $key=>$trans)
                        <?php

                        $payment = (object)$trans->payment_channel;

                        if ($trans->status == 'approved') {
                            $status = '<strong class="text-success">' . ucwords($trans->status) . '</strong>';
                            $action = '-';
                        } else if ($trans->status == 'rejected') {
                            $status = '<strong class="text-danger">' . ucwords($trans->status) . '</strong>';
                            $action = '-';
                        } else {

                            $status = '<strong class="text-warning">' . ucwords($trans->status) . '</strong>';
                        } ?>
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $trans->transaction_id }}</td>
                            <td>{!! mSign($trans->amount) !!}</td>
                            <td>{{ ucwords($trans->receiver_name)}}</td>
                            <td>{{ ucwords(str_replace('_',' ',$trans->payment_mode))}}</td>
                            <td>{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</td>
                            <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
                                <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
                            </td>
                            <td><?= (!empty($payment->bank_name)) ? $payment->bank_name : '-' ?></td>
                            <td>{!! $status !!}</td>
                            <td>{{ date('d,M y H:i A',$trans->created) }}</td>

                        </tr>
                        @endforeach
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

            <!-- for loader -->
            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body">
                <p>Download sample Payout Transaction Import(CSV) file : <a href="{{ url('retailer/sample-csv') }}" class="text-green">Download</a></p>
                <form id="import" action="{{ url('retailer/payout-import') }}" method="post" enctype="multipart/form-data">
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
        $('form#import')[0].reset();
        let url = '{{ url("retailer/payout-import") }}';
        $('form#import').attr('action', url);
        $('#importModal').modal('show');
    })
</script>

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

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
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

<?php
$bank_names = [
    'Bank of Baroda', 'Bank of India', 'Bank of Maharashtra', 'Canara Bank', 'Central Bank of India',
    'Indian Bank', 'Indian Overseas Bank', 'Punjab & Sind Bank', 'Punjab National Bank', 'State Bank of India', 'UCO Bank',
    'Union Bank of India', 'Axis Bank Ltd', 'Bandhan Bank Ltd', 'CSB Bank Ltd', 'City Union Bank Ltd', 'DCB Bank Ltd', 'Dhanlaxmi Bank Ltd',
    'Federal Bank Ltd', 'HDFC Bank Ltd', 'ICICI Bank Ltd', 'Induslnd Bank Ltd', 'IDFC First Bank Ltd', 'Jammu & Kashmir Bank Ltd',
    'Karnataka Bank Ltd', 'Karur Vysya Bank Ltd', 'Kotak Mahindra Bank Ltd', 'Lakshmi Vilas Bank Ltd', 'Nainital Bank Ltd', 'RBL Bank Ltd',
    'South Indian Bank Ltd', 'Tamilnad Mercantile Bank Ltd', 'YES Bank Ltd', 'IDBI Bank Ltd', 'Au Small Finance Bank Limited', 'Capital Small Finance Bank Limited',
    'Equitas Small Finance Bank Limited', 'Suryoday Small Finance Bank Limited', 'Ujjivan Small Finance Bank Limited', 'Utkarsh Small Finance Bank Limited',
    'ESAF Small Finance Bank Limited', 'Fincare Small Finance Bank Limited', 'Jana Small Finance Bank Limited', 'North East Small Finance Bank Limited', 'Shivalik Small Finance Bank Limited',
    'India Post Payments Bank Limited', 'Fino Payments Bank Limited', 'Paytm Payments Bank Limited', 'Airtel Payments Bank Limited'
];
?>

<script>
    $('#payment_channel').change(function() {
        var payment_channel = $(this).val();
        if (payment_channel == 'bank_account') {
            $('#payment_channel_field').html(`<div class="form-group">
            <label>Bank Name</label>
            <select name="payment_channel[bank_name]" class="form-control form-control-sm" required>
            <option value=''>Select Bank Name</option>
            <?php foreach ($bank_names as $name) {
                echo '<option value=' . $name . '>' . $name . '</option>';
            } ?>
            </select>
            </div>
            <div class="form-group">
            <label>Account Number</label>
            <input type="number" name="payment_channel[account_number]" class="form-control form-control-sm" placeholder="Enter Account Number">
            </div>
            <div class="form-group">
            <label>IFSC Code</label>
            <input type="text" name="payment_channel[ifsc_code]" class="form-control form-control-sm" placeholder="Enter IFSC Code">
            </div>`);
        } else if (payment_channel == 'upi') {
            $('#payment_channel_field').html(`<div class="form-group">
            <label>UPI Id</label>
            <input type="text" name="payment_channel[upi_id]" class="form-control form-control-sm" placeholder="Enter UPI ID">
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
            // $('#upload_docs').html(`<div class="form-group">
            // <label>Pancard Number</label>
            // <input type="text" name="pancard_no" class=" form-control form-control-sm" placeholder="Enter Pancard Number" required>
            // </div>
            // <div class="form-group">
            // <label>Uploade Pancard</label>
            // <div class="input-group">
            // <div class="custom-file">
            // <input type="file" name="pancard" class=" custom-file-input custom-file-input-sm" id="attachment" required>
            // <label class="custom-file-label" for="exampleInputFile">Choose file</label>
            // </div>
            // </div>
            // <span id="attachment_msg" class="custom-text-danger"></span>
            // </div>`);
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
                    $('form#add_customer')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 1000)
                }
            }
        });
    });

    /*end form submit functionality*/


    /*start import functionality*/
    $("form#import").submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        var url = $(this).attr('action');

        $.ajax({
            data: formData,
            type: "post",
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
                if (res.file) {
                    $('#fileMsg').html(res.file);
                } else {
                    $('#fileMsg').html('');
                }
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
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            }
        });
    });
    /*end import functionality*/
</script>

@endpush
@endsection