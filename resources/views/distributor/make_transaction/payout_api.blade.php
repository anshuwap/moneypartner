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
<div class="modal fade" id="payout_api_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                <form id="add_payout_api" action="{{ url('retailer/payout-api') }}" method="post">
                    @csrf
                    <div id="put"></div>
                    <div class="row">

                        <div class="col-md-12">

                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" placeholder="Enter Amount" id="amount_payout_api" required name="amount" class="form-control form-control-sm">
                                    <span id="pauout_api_amount_msg" class="custom-text-danger"></span>
                                </div>

                                <div id="charges_payout_api"></div>
                                <div id="payout_api_upload_docs"></div>

                                <div class="form-group">
                                    <label>Receiver Name</label>
                                    <input type="text" placeholder="Enter Receiver Name" id="receiver" required name="receiver_name" class="form-control form-control-sm">
                                    <span id="receiver_msg" class="custom-text-danger"></span>
                                </div>

                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <select name="bank_name" class="form-control form-control-sm" required>
                                        <option value=''>Select Bank Name</option>
                                        <?php foreach ($bank_names as $name) {
                                            echo '<option value="' . $name . '">' . $name . '</option>';
                                        } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Account Number</label>
                                    <input type="number" name="account_number" class="form-control form-control-sm" placeholder="Enter Account Number">
                                </div>
                                <div class="form-group">
                                    <label>IFSC Code</label>
                                    <input type="text" name="ifsc_code" class="form-control form-control-sm" placeholder="Enter IFSC Code">
                                </div>
                            </div>

                        <div class="col-md-12 mt-2">
                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-success btn-sm" id="submit_payout_api" value="Submit">
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
    $('#amount_payout_api').keyup(function(e) {
        e.preventDefault();

        var amount = $(this).val();
        // payout_transction_api(amount);

        if (amount >= 25000 && amount < 200000) {
            // $('#payout_api_upload_docs').html(`<div class="form-group">
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
            $('#add_payout_api input,select').attr('disabled', 'disabled');
            $('#pauout_api_amount_msg').html('Alowed only 2 lakh Per Month.');
            $('#amount_payout_api').removeAttr('disabled');
        } else {
            $('#payout_api_upload_docs').html(``);
            $('#pauout_api_amount_msg').html(``);
            $('#add_payout_api input,select').removeAttr('disabled');
        }
    })


    //for check retailer fee detials
    function payout_transction_api(amount) {

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
                    $('#charges_payout_api').html(`Rs. ${res.charges} Transaction Fees
        <div class="form-group">
        <label>Transaction Amount</label>
        <input type="text" readonly name="" value="${parseInt(amount)+ parseInt(res.charges)}" class="form-control form-control-sm">
        </div>`);

                } else if (res.status == 'error') {
                    $('#charges_payout_api').html(res.msg);
                }
            }
        })
    }


    $('#create_retailer').click(function(e) {
        e.preventDefault();
        $('form#add_payout_api')[0].reset();
        let url = '{{ url("retailer/payout-api") }}';
        $('#heading_bank').html('Transaction Details');
        $('#put').html('');
        $('form#add_payout_api').attr('action', url);
        $('#submit_payout_api').val('Submit');
        $('#payout_api_modal').modal('show');
    })


    /*start form submit functionality*/
    $("form#add_payout_api").submit(function(e) {
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
                    $('form#add_payout_api')[0].reset();
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