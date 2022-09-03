@push('modal')

<!-- Modal -->
<div class="modal fade" id="MRechargeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Mobile/DTH Recharge</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- for loader -->
            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body">
                <form id="MRecharge" action="{{ url('retailer/m-recharge') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="form-row">
                        <label>Select Operator</label>
                        <div class="form-group col-md-12">
                            <select class="form-control form-control-sm" name="operator">
                                <option value="">Select</option>
                                <option value="1">AIRTEL</option>
                                <option value="2">IDEA</option>
                                <option value="3">BSNL TOPUP</option>
                                <option value="4">BSNL Special</option>
                                <option value="5">JIO</option>
                                <option value="6">VODAFONE</option>
                                <option value="7">AIRTEL DTH</option>
                                <option value="8">DISH TV</option>
                                <option value="10">SUN DIRECT</option>
                                <option value="11">TATA SKY</option>
                                <option value="12">VIDEOCON D2H</option>
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Mobile/DTH No</label>
                            <input type="number" class="form-control form-control-sm" name="mobile_no" placeholder="Enter Mobile/DTH Number">
                            <span id="" class="custom-text-danger"></span>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Amount</label>
                            <input type="number" class="form-control form-control-sm" name="amount" placeholder="Enter Amount">
                            <span id="" class="custom-text-danger"></span>
                        </div>

                        <div class="form-group col-md-2 text-center">
                            <input type="submit" class="btn btn-success btn-sm" value="Recharge">
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $('#mobileRecharge').click(function(e) {
        e.preventDefault();
        $('#MRechargeModal').modal('show');
    })


    /*start form submit functionality*/
    $("form#MRecharge").submit(function(e) {
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
</script>

@endpush