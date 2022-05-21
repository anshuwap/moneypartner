@push('modal')

<!-- Modal -->
<div class="modal fade" id="clame" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_bank">Claim Payout Refund</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body">
                <form id="makeClame" action="{{ url('retailer/payout-clame') }}" method="post">
                    @csrf
                    <div id="put"></div>
                    <div class="row ">
                        <input type="hidden" id="trans_id22" name="trans_id">
                        <div class="col-md-12">
                            <div class="text-center d-flex">
                                <div><strong>Transaction ID :-</strong>&nbsp;<span id="trans_id"></span></div>
                                &nbsp; &nbsp; <div><strong>Amount :-</strong>&nbsp;<span id="amount121"></span></div>
                            </div>
                            <div class="pt-3 pl-5 pr-5">

                                <div class="form-group mb-3">
                                    <label class="text-center">Enter PIN</label>
                                    <div class="cover-otp d-flex ">
                                        <input type="number" name="pin[]" type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==1) return false;" class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="cf11p">
                                        <input type="number" name="pin[]" type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==1) return false;" class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="cf22p">
                                        <input type="number" name="pin[]" type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==1) return false;" class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="cf33p">
                                        <input type="number" name="pin[]" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==1) return false;" type="number" class="otp form-control rounded-0 border-top-0 border-right-0 border-left-0 m-2" placeholder="0" id="cf44p">
                                    </div>
                                    <span id="otp_verify" class="text-success"></span>
                                </div>
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-success btn-sm disabled" id="cverify"><i class="fas fa-compress-arrows-alt"></i>&nbsp;Submit</button>

                                </div>
                            </div>
                        </div>

                    </div>
            </div>

            </form>
        </div>
    </div>
</div>

<script>
    $('.clame').click(function() {

        var trans_id = $(this).attr('trans_id');
        var transaction_id = $(this).attr('trans_no');
        var amount = $(this).attr('amount');

        $('#trans_id22').val(trans_id);
        $('#amount121').html('<i class="fas fa-rupee-sign" style="font-size: 10px; color: #696b74;"></i>&nbsp;' + amount);
        $('#trans_id ').html(transaction_id);
        $('#clame').modal('show');

    })

    /*start focus pointer to new field (functionality)*/
    $('#cf11p').keyup(function() {
        if ($('#cf11p').val().length == 1) {
            $('#cf22p').focus();
        }
    });
    $('#cf22p').keyup(function(e) {
        if ($('#cf22p').val().length == 1) {
            $('#cf33p').focus();
        }
        if (e.keyCode == 8) {
            $('#cf11p').focus();
            $('#cf11p').val();
        }
    });
    $('#cf33p').keyup(function(e) {
        if ($('#cf33p').val().length == 1) {
            $('#cf44p').focus();
        }
        if (e.keyCode == 8) {
            $('#cf22p').focus();
            $('#cf22p').val();
        }
    });
    $('#cf44p').keyup(function(e) {
        if ($('#cf44p').val().length == 1) {
            $('#cverify').focus();
            $("#cverify").removeClass("disabled");
        }
        if (e.keyCode == 8) {
            $('#cf33p').focus();
            $('#cf33p').val();
        }
    });
    /*end focus pointer to new field (functionality)*/


    $("form#makeClame").submit(function(e) {
        e.preventDefault();

        var exit_pin = '{{ Auth::user()->pin }}';

        var pin1 = $('#cf11p').val().trim();
        var pin2 = $('#cf22p').val().trim();
        var pin3 = $('#cf33p').val().trim();
        var pin4 = $('#cf44p').val().trim();
        var pin = pin1 + pin2 + pin3 + pin4;
        if (parseInt(pin) != parseInt(exit_pin)) {
            Swal.fire(
                `Error!`,
                'Pin is not Verified!',
                `error`,
            )
            return false;
        }

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
                    $('form#makeClame')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 1000)
                }
            }
        });
    });
</script>
@endpush