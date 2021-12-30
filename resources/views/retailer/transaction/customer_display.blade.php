@extends('retailer.layouts.app')

@section('content')
@section('page_heading', 'Customer List')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">

            <div class="covertabs-btn __web-inspector-hide-shortcut__">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a href="{{ url('retailer/customer-trans') }}" class="nav-link active">Customer Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('retailer/retailer-trans') }}" class="nav-link">Retailer Transaction</a>
                    </li>
                </ul>
                <div class="add-btn">
                <a href="javascript:void(0);" class="btn btn-sm btn-success mr-4" id="create_customer"><i class="fas fa-plus-circle"></i>&nbsp;Add</a>
                </div>
            </div>


            <div class="card-body table-responsive py-4 table-sm">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Customer Name</th>
                            <th>Mobile No.</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($customer_trans as $key=>$trans)
                        <tr data-widget="expandable-table" aria-expanded="false">
                            <td>{{ ++$key }}</td>
                            <td>{{ ucwords($trans->customer_name) }}</td>
                            <td>{{ $trans->mobile_number }}</td>
                            <td>{{ date('Y-m-d',$trans->created) }}</td>
                        </tr>

                        <tr class="expandable-body d-none">
                            <td colspan="8">
                                <p style="display: none; margin-top: -41px;">
                                <table class="table table-sm bg-secondary" style="font-size: 13px;">
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Sender Name</th>
                                        <th>Amount</th>
                                        <th>Receiver Name</th>
                                        <th>Payment Mode</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                    </tr>
                                    <?php
                                    if(!empty($trans->trans_details)){
                                    foreach($trans->trans_details as $ke=>$detail){

                                    if ($detail['status'] == 'approved'){
                                    $status = '<strong class="text-success">' . ucwords($detail['status']) . '</strong>';
                                    }else if($detail['status'] == 'rejected'){
                                    $status = '<strong class="text-danger">' . ucwords($detail['status']) . '</strong>';
                                    }else{
                                    $status = '<strong class="text-warning">' . ucwords($detail['status']) . '</strong>';
                                    }
                                    ?>
                                    <tr>
                                        <td>{{ ++$ke }}</td>
                                        <td>{{ ucwords($detail['sender_name'] ) }}</td>
                                        <td>{!! mSign($detail['amount']) !!}</td>
                                        <td>{{ ucwords($detail['receiver_name'] ) }}</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $detail['payment_mode'])) }}</td>
                                        <td><?=$status ?></td>
                                        <td>{{ date('Y-m-d',$detail['created'])}}</td>
                                    </tr>
                                <?php } } ?>
                                </table>
                                </p>
                            </td>
                        </tr>



                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->

@push('modal')

<!-- Modal -->
<div class="modal fade" id="add_bank_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_bank">Customer Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add_customer" action="{{ url('retailer/customer-trans') }}" method="post">
                    @csrf
                    <div id="put"></div>
                    <div class="row">
                        <div class="col-md-6"><strong>Sender Details</strong>

                            <div class="border p-3">
                                <div class="form-group">
                                    <label>Mobile Number</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" placeholder="Enter Mobile Number" id="mobile_number" required name="mobile_number" class="form-control form-control-sm">
                                        <span class="input-group-append">
                                            <a href="javascript:void(0)" class="btn btn-info btn-flat disabled" id="send_otp" data-toggle="tooltip" data-placement="bottom" title="Send OTP"><i class="fas fa-paper-plane"></i></a>
                                        </span>
                                    </div>
                                    <div id="details"> </div>
                                    <span id="mobile_numberMsg" class="text-success"></span>
                                    <span id="mobile_number_msg" class="custom-text-danger"></span>
                                </div>


                                <div class="form-group d-none" id="otp-field">

                                </div>

                                <div class="form-group">
                                    <label>Sender Name</label>
                                    <input type="text" placeholder="Enter Sender Name" id="sender_name" required name="sender_name" class="form-control form-control-sm" disabled>
                                    <span id="sender_name_msg" class="custom-text-danger"></span>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-6"><strong>Receiver Details</strong>
                            <div class="border p-3">

                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" placeholder="Enter Amount" id="amount" required name="amount" class="form-control form-control-sm" disabled>
                                    <span id="amount_msg" class="custom-text-danger"></span>
                                </div>

                                <div id="charges"></div>
                                <div id="upload_docs">

                                </div>

                                <div class="form-group">
                                    <label>Receiver Name</label>
                                    <input type="text" placeholder="Enter Receiver Name" id="receiver" required name="receiver_name" class="form-control form-control-sm" disabled>
                                    <span id="receiver_msg" class="custom-text-danger"></span>
                                </div>

                                <div class="form-group">
                                    <label>Select Payment Channel</label>
                                    <select name="payment_mode" class="form-control form-control-sm" id="payment_channel" disabled>
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
                                <input type="submit" class="btn btn-success btn-sm" disabled id="submit_customer" value="Submit">
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


function transactionFeeDetails(amount){

    var amount = (amount)?amount:0;
    $.ajax({
        url:"<?=url('retailer/fee-details')?>",
        data:{'amount':amount},
        dataType:"JSON",
        type:"GET",
        success:function(res){

            if(res.status =='success'){
            $('#charges').html(`Rs. ${res.charges} Transaction Fees
            <div class="form-group">
            <label>Transaction Amount</label>
            <input type="text" readonly name="" value="${parseInt(amount)+ parseInt(res.charges)}" class="form-control form-control-sm">
            </div>`);

            }else if(res.status =='error'){
                $('#charges').html(res.msg);
            }
        }
    })
}


    /*start otp varifaction functionality*/
    $('#send_otp').click(function() {
        var phoneNo = $('#mobile_number').val();
        var mob = /^[1-9]{1}[0-9]{9}$/;
        if (phoneNo == "" || phoneNo == null) {
            $('#otp_msg').html("Please enter Mobile No.");
            return false;
        } else if (phoneNo.length < 10 || phoneNo.length > 10) {
            $('#otp_msg').html("Mobile No. is not valid, Please Enter 10 Digit Mobile No.");
            return false;
        } else if (mob.test(phoneNo) == false) {
            $('#otp_msg').html("Please enter valid mobile number.");
            return false;
        } else {
            $.ajax({
                url: "{{ url('retailer/send-otp') }}",
                data: {
                    'mobile_no': phoneNo
                },
                type: 'GET',
                dataType: "JSON",
                success: function(res) {

                    if (res.status == 'success') {
                        $('#otp_msg').html('');
                        alert(res.otp);
                        $('#mobile_numberMsg').html(res.msg);
                        $('#otp-field').removeClass('d-none');
                        $('#otp-field').html(`<label>OTP</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" placeholder="Enter OTP" id="otp" required name="otp" class="form-control form-control-sm">
                                        <span class="input-group-append">
                                            <a href="javascript:void(0)" id="verify" class=" btn btn-success btn-flat" data-toggle="tooltip" data-placement="bottom" title="Verify"><i class="fas fa-check-square"></i></a>
                                        </span>
                                    </div>

                                    <span id="otp_verify" class="text-success"></span>
                                    <span id="otp_msg" class="custom-text-danger"></span>`);
                        return false;
                    } else if (res.status == 'error') {
                        $('#mobile_numberMsg').html(res.msg);
                        return false;
                    } else if (res.status == 'detail') {
                        $('#details').html(res.data);
                        $('#add_customer input,select').removeAttr('disabled');
                        $('#otp-field').html(``);
                        $('#mobile_numberMsg').html(``);
                    }
                }
            })
        }
    })

    $('#mobile_number').keyup(function() {
        var phoneNo = $(this).val();
        var mob = /^[1-9]{1}[0-9]{9}$/;
        if (phoneNo == "" || phoneNo == null) {
            $('#mobile_number_msg').html("Please enter Mobile No.");
            $('#send_otp').addClass('disabled');
            return false;
        } else if (phoneNo.length < 10 || phoneNo.length > 10) {
            $('#mobile_number_msg').html("Mobile No. is not valid, Please Enter 10 Digit Mobile No.");
            $('#send_otp').addClass('disabled');
            return false;
        } else if (mob.test(phoneNo) == false) {
            $('#mobile_number_msg').html("Please enter valid mobile number.");
            $('#send_otp').addClass('disabled');
            return false;
        } else {
            $('#mobile_number_msg').html('');
            $('#send_otp').removeClass('disabled')
            return false;
        }
    })


   $(document).on('click','#verify',function(){
        var mobile_no = $('#mobile_number').val();
        var otp = $('#otp').val();

        $.ajax({
            url: "{{ url('retailer/verify-mobile') }}",
            data: {
                'otp': otp,
                'mobile_no': mobile_no
            },
            type: 'GET',
            dataType: "JSON",
            success: function(res) {

                if (res.status == 'success') {
                    $('#otp_msg').html('');
                    $('#otp_verify').html(res.msg);
                    $('#add_customer input,select').removeAttr('disabled');
                    return false;
                } else if (res.status == 'error') {
                    $('#otp_verify').html(res.msg);
                    return false;
                }
            }
        });
    });


    $(document).on('keyup','#otp',function(){
        var otp = $(this).val();
        if (otp == "" || otp == null) {
            $('#otp_msg').html("Please enter OTP.");
            $('#verify').addClass('disabled');
            return false;
        } else if (otp.length < 4 || otp.length > 4) {
            $('#otp_msg').html("Please Enter 4 Digit OTP.");
            $('#verify').addClass('disabled');
            return false;
        } else {
            $('#otp_msg').html('');
            $('#verify').removeClass('disabled')
            return false;
        }
    })
    /*end otp varifaction functionality*/


    $('#create_customer').click(function(e) {
        e.preventDefault();
        $('form#add_customer')[0].reset();
        let url = '{{ url("retailer/customer-trans") }}';
        $('#heading_bank').html('Transaction Details');
        $('#put').html('');
        $('form#add_customer').attr('action', url);
        $('#submit_customer').val('Submit');
        $('#add_bank_modal').modal('show');
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
                $('#submit_customer').val(`Saving...`);
            },
            success: function(res) {
                //hide loader
                $('.has-loader').removeClass('has-loader-active');
                $('#submit_customer').val(`Submit`);
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