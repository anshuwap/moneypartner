@push('modal')

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
    'India Post Payments Bank Limited', 'Fino Payments Bank Limited', 'Paytm Payments Bank Limited', 'The Panipat Urban Co Operative bank', 'Syndicate Bank', 'Airtel Payments Bank Limited'
];
?>
<!-- Modal -->
<div class="modal fade" id="add_bank_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-scrollable modal-lg " role="document">
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
                <form id="add_customer" action="{{ url('retailer/dmt-trans') }}" method="post">
                    @csrf
                    <div id="put"></div>
                    <div class="row">
                        <div class="col-md-6 dmt-form"><strong>Sender Details</strong>
                            <div id="preview-input"><input type="hidden" id="preview-field" value="preview" name="preview"></div>

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

                        <div class="col-md-6 dmt-form" id="dmt-form"><strong>Beneficiary Details</strong>
                            <div class="border p-3">

                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" placeholder="Enter Amount" id="amount" required name="amount" class="form-control form-control-sm" disabled>
                                    <span id="amount_msg" class="custom-text-danger"></span>
                                </div>

                                <!-- <div id="charges"></div> -->
                                <div id="upload_docs">

                                </div>

                                <div class="form-group">
                                    <label>Beneficiary Name</label>
                                    <input type="text" placeholder="Enter Beneficiary Name" id="receiver" required name="receiver_name" class="form-control form-control-sm" disabled>
                                    <span id="receiver_msg" class="custom-text-danger"></span>
                                </div>

                                <!-- <div class="form-group">
                                    <label>Select Payment Channel</label>
                                    <select name="payment_mode" class="form-control form-control-sm" id="payment_channel" disabled>
                                        <option value="">Select</option>
                                        <option value="bank_account">Bank Account</option>
                                      <option value="upi">UPI</option> -->
                                <!-- </select>
                                    <span id="receiver_msg" class="custom-text-danger"></span>
                                </div>
                                <div id="payment_channel_field"> -->

                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <select name="payment_channel[bank_name]" disabled class="form-control form-control-sm" required>
                                        <option value=''>Select Bank Name</option>
                                        <?php foreach ($bank_names as $name) {
                                            echo '<option value="' . $name . '">' . $name . '</option>';
                                        } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Account Number</label>
                                    <input type="number" name="payment_channel[account_number]" disabled class="form-control form-control-sm" placeholder="Enter Account Number">
                                </div>
                                <div class="form-group">
                                    <label>IFSC Code</label>
                                    <input type="text" name="payment_channel[ifsc_code]" disabled class="form-control form-control-sm" placeholder="Enter IFSC Code">
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="com-md-12 bordered-1 d-none" id="preview">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <td>Sender Name</td>
                                        <td id="sender_name1">Sender Name</td>
                                    </tr>
                                    <tr>
                                        <td>Mobile Number</td>
                                        <td id="mobile_number1"></td>
                                    </tr>
                                    <tr>
                                        <td>Amount</td>
                                        <td id="amount1"></td>
                                    </tr>
                                    <tr>
                                        <td>Fess</td>
                                        <td id="fees"></td>
                                    </tr>
                                    <tr>
                                        <td>Receiver Name</td>
                                        <td id="receiver_name"></td>
                                    </tr>
                                    <tr>
                                        <td>Bank Name</td>
                                        <td id="bank_name"></td>
                                    </tr>
                                    <tr>
                                        <td>Account Number</td>
                                        <td id="account_number"></td>
                                    </tr>
                                    <tr>
                                        <td>IFSC Code</td>
                                        <td id="ifsc_code"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="card p-4">
                                    <div class="form-group mb-3">
                                        <label class="text-center">Enter PIN</label>
                                        <div class="cover-otp d-flex ">
                                            <input type="number" name="pin[]" type=" number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==1) return false;"  class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="f1">
                                            <input type="number" name="pin[]" type=" number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==1) return false;"  class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="f2">
                                            <input type="number" name="pin[]" type=" number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==1) return false;"  class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="f3">
                                            <input type="number" name="pin[]" type=" number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==1) return false;"  class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="f4">
                                        </div>
                                        <span id="otp_verify" class="text-success"></span>
                                    </div>
                                    <div class="form-group text-center">
                                        <!-- <input type="submit" class="btn btn-success btn-sm" value="Verify"> -->
                                        <a href="javascript:void(0);" class="btn btn-sm btn-warning" id="back"><i class="far fa-arrow-alt-circle-left"></i>&nbsp;Back</a>
                                        <button type="submit" class="btn btn-success btn-sm disabled" id="login"><i class="fas fa-compress-arrows-alt"></i>&nbsp;Verify & Send</button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-12 mt-2">
                        <div class="form-group text-center" id="submit-btn">
                            <input type="submit" class="btn btn-success btn-sm" disabled id="submit_customer" value="Preview">
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
            <select name="payment_channel[bank_name]" class="form-control form-control-sm" required>
            <option value=''>Select Bank Name</option>
            <?php foreach ($bank_names as $name) {
                echo '<option value="' . $name . '">' . $name . '</option>';
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
        // transactionFeeDetails(amount);

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


    $(document).on('click', '#verify', function() {
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


    $(document).on('keyup', '#otp', function() {
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
        let url = '{{ url("retailer/dmt-trans") }}';
        $('#heading_bank').html('Transaction Details');
        $('#put').html('');
        $('form#add_customer').attr('action', url);
        $('#submit_customer').val('Preview');
        // openPinPopup();
        $('#add_bank_modal').modal('show');

    })


    //back to dmt form
    $('#back').click(function(e) {
        e.preventDefault();
        $('.dmt-form').removeClass('d-none');
        $('#preview').addClass('d-none');
        $('#submit-btn').removeClass('d-none');
        $('#preview-input').html('<input type="hidden" id="preview-field" value="preview" name="preview">');
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
                if (res.status == 'error' || res.status == 'success') {
                    Swal.fire(
                        `${res.status}!`,
                        res.msg,
                        `${res.status}`,
                    )
                }
                /*End Status message*/

                //for preview page
                console.log(res);
                if (res.status === 'preview') {
                    $('.dmt-form').addClass('d-none');
                    $('#preview').removeClass('d-none');
                    $('#submit-btn').addClass('d-none');
                    $('#preview-field').remove();
                    $('#sender_name1').html(res.response.sender_name);
                    $('#mobile_number1').html(res.response.mobile_number);
                    $('#amount1').html(res.response.amount);
                    $('#fees').html(res.response.fees);
                    $('#bank_name').html(res.response.payment_channel.bank_name);
                    $('#account_number').html(res.response.payment_channel.account_number);
                    $('#ifsc_code').html(res.response.payment_channel.ifsc_code);
                    $('#receiver_name').html(res.response.receiver_name);
                }
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


    $('.popover-dismiss').popover({
        trigger: 'focus'
    })
</script>

<script>
    /*start focus pointer to new field (functionality)*/
    $('#f1').keyup(function() {
        if ($('#f1').val().length == 1) {
            $('#f2').focus();
        }
    });
    $('#f2').keyup(function(e) {
        if ($('#f2').val().length == 1) {
            $('#f3').focus();
        }
         if (e.keyCode == 8) {
            $('#f1p').focus();
            $('#f1p').val();
        }
    });
    $('#f3').keyup(function(e) {
        if ($('#f3').val().length == 1) {
            $('#f4').focus();
        }
         if (e.keyCode == 8) {
            $('#f2p').focus();
            $('#f2p').val();
        }
    });
    $('#f4').keyup(function(e) {
        if ($('#f4').val().length == 1) {
            $('#login').focus();
            $("#login").removeClass("disabled");
        }
         if (e.keyCode == 8) {
            $('#f3p').focus();
            $('#f3p').val();
        }
    });
    /*end focus pointer to new field (functionality)*/
</script>

@endpush