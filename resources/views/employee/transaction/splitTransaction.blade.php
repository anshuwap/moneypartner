@push('modal')
<style>
    .mt-18 {
        margin-top: 18px;
    }
</style>
<div class="modal fade" id="slitPopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 1100px !important" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_bank_dashboard">Split Amount</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body">
                <form id="split" action="{{ url('employee/split-transaction') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="trans_id" name="trans_id">

                            <div id="split-field">
                                <div class="form-row">

                                    <div class="col-md-3">
                                        <span>Amount</span>
                                        <input type="text" value="" required name="response[0][amount]" class="form-control form-control-sm" placeholder="Enter Amount">
                                    </div>
                                    <div class="col-md-2">
                                        <span>Action</span>
                                        <select name="response[0][status]" attr="0" id="action" class="action form-control form-control-sm" required>
                                            <option value="">Select</option>
                                            <option value="success">Success</option>
                                            <option value="pending">Pending</option>
                                            <option value="rejected">Rejected</option>
                                            <option value="refund_pending">Refund Pending</option>
                                        </select>
                                        <span id="status_msg" class="custom-text-danger"></span>
                                    </div>
                                    <div class="form-group col-md-2" id="payment-channel-0">
                                        <span>Payment Channel</span>
                                        <select name="response[0][payment_mode]" attr="0" class="form-control form-control-sm" id="pc-0" required>
                                            <option value="">Select</option>
                                            <?php foreach ($payment_channel as $channel) {
                                                echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                                            } ?>
                                        </select>
                                        <span id="payment_channel_msg" class="custom-text-danger"></span>
                                    </div>

                                    <div class="form-group col-md-2" id="utr-0">
                                        <span>UTR/Transaction</span>
                                        <input type="text" placeholder="UTR/Transaction" attr="0" required id="utr1-0" name="response[0][utr_number]" class="form-control form-control-sm">
                                        <span id="utr_transaction_msg" class="custom-text-danger"></span>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <span>Comment</span>
                                        <select name="response[0][msg]" class="form-control form-control-sm" id="split-comment-0">
                                            <option value="">Select</option>
                                        </select>
                                        <span id="comment_msg" class="custom-text-danger"></span>
                                    </div>

                                    <div class="col-md-1 mt-18">
                                        <!-- <a href="javascript:void(0);" class="btn btn-danger btn-sm remove" k="0"><i class="fas fa-solid fa-minus"></i></a> -->
                                    </div>

                                </div>
                            </div>

                            <hr />
                            <div class="form-group text-center">
                                <label><a href="javascript:void(0);" class="text-success" id="add-more" j="0"><i class="fas fa-plus-square"></i>&nbsp;Add More</a></label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-success btn-sm" value="Submit">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $('.split').click(function() {
        // $('#approve_trans_')[0].reset();
        var trans_id = $(this).attr('_id');
        $('#trans_id').val(trans_id);
        $('#slitPopup').modal('show');
    })

    $(document).on('change', '.action', function(e) {
        e.preventDefault();
        var type = $(this).val();
        var index = $(this).attr('attr');

        if (type === 'pending') {
            $('#utr-' + index).hide()
            $('#utr1-' + index).prop('required', false);
        } else if (type === 'rejected' || type === 'refund_pending') {
            $('#payment-channel-' + index).hide();
            $('#pc-' + index).prop('required', false);
            $('#utr-' + index).hide();
            $('#utr1-' + index).prop('required', false);
        } else if (type === 'success') {
            $('#payment-channel-' + index).show();
            $('#utr-' + index).show();
            $('#utr1-' + index).prop('required', true);
            $('#pc-' + index).prop('required', true);
        }
        $.ajax({
            url: "<?= url('employee/a-trans-comment') ?>",
            data: {
                'type': type
            },
            type: 'GET',
            dataType: "json",
            success: function(res) {
                $('#split-comment-' + index).html(res);
            }
        })
    })

    $('#add-more').click(function() {
        var i = 1;
        var j = $(this).attr('j');
        var x = parseInt(j) + parseInt(1);
        $('#split-field').append(`<div class="form-row" id="remove-${x}">

                                    <div class="col-md-2">
                                        <span>Amount</span>
                                        <input type="text" value="" required name="response[${x}][amount]" class="form-control form-control-sm" placeholder="Enter Amount">
                                    </div>

                                     <div class="col-md-2">
                                        <span>Charges</span>
                                        <input type="text" value="" required name="response[0][charges]" class="form-control form-control-sm" placeholder="Enter charges">
                                    </div>

                                    <div class="col-md-1">
                                        <span>Action</span>
                                        <select name="response[${x}][status]" attr="${x}" id="action" class="action form-control form-control-sm" required>
                                            <option value="">Select</option>
                                            <option value="success">Success</option>
                                            <option value="pending">Pending</option>
                                            <option value="rejected">Rejected</option>
                                            <option value="refund_pending">Refund Pending</option>
                                        </select>
                                        <span id="status_msg" class="custom-text-danger"></span>
                                    </div>
                                    <div class="form-group col-md-2" id="payment-channel-${x}">
                                        <span>Payment Channel</span>
                                        <select name="response[${x}][payment_mode]" attr="${x}" class="form-control form-control-sm" id="pc-${x}" required>
                                            <option value="">Select</option>
                                            <?php foreach ($payment_channel as $channel) {
                                                echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                                            } ?>
                                        </select>
                                        <span id="payment_channel_msg" class="custom-text-danger"></span>
                                    </div>

                                    <div class="form-group col-md-2" id="utr-${x}">
                                        <span>UTR/Transaction</span>
                                        <input type="text" placeholder="UTR/Transaction" attr="${x}" required id="utr1-${x}" name="response[${x}][utr_number]" class="form-control form-control-sm">
                                        <span id="utr_transaction_msg" class="custom-text-danger"></span>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <span>Comment</span>
                                        <select name="response[${x}][msg]" class="form-control form-control-sm" id="split-comment-${x}">
                                        </select>
                                        <span id="comment_msg" class="custom-text-danger"></span>
                                    </div>

                                    <div class="col-md-1 mt-18">
                                        <a href="javascript:void(0);" class="btn btn-danger btn-sm remove" k="${x}"><i class="fas fa-solid fa-minus"></i></a>
                                    </div>

                                </div>`);
        $(this).attr('j', x);
    });

    $(document).on('click', '.remove', function() {
        var k = $(this).attr('k');
        // let x = parseInt(k) - 1;
        $('#remove-' + k).remove();
        // $('#add-more').attr('j', x);
    });

    /*start form submit functionality*/
    $("form#split").submit(function(e) {
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
                    $('form#split')[0].reset();
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