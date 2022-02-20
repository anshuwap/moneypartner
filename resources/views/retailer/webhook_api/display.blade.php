@extends('retailer.layouts.app')
@section('content')
@section('page_heading', 'Spent Amount Topup List')


<div class="row">

    <div class="col-md-6 mt-1">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Integrate Webhook</h3>
            </div>

            <div class="card-body">
                <div class="col-sm-12">
                    <div class="position-relative p-3 bg-teal disabled color-palette" style="height: 180px">
                        <div class="ribbon-wrapper">
                            <div class="ribbon bg-fuchsia color-palette">
                                Webhook
                            </div>
                        </div>
                        Webhook URL <br>
                        <small>Integrate Your Webhook Link Here.</small>

                        <div class="form-group mr-4">
                            @if(!empty($webhook))
                            <input type="hidden" id="webhook_id" value="{{ $webhook->_id }}" />
                            @endif
                            <div class="input-group input-group-sm">
                                <input type="text" value="{{ (!empty($webhook->webhook_url))?$webhook->webhook_url:''}}" class="form-control form-control form-control-sm" placeholder="Enter Webhook URL" name="webhool_url" id="webhook_url">
                                <span class="input-group-append ">
                                    <button type="button" class="btn btn-info btn-flat" id="integrate">submit</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-6 mt-1">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Api Documents</h3>
            </div>

            <div class="card-body">

                <div id="accordion">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h4 class="card-title w-100">
                                <a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseOne" aria-expanded="false">
                                    Document of Signle Payout Api
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="collapse" data-parent="#accordion" style="">
                            <div class="card-body">
                                {
                                "amount":"100",
                                "receiver_name":"Demo21",
                                "payment_mode":"bank_account",
                                "payment_channel":{
                                "bank_name":"SBI Bank",
                                "account_number":"9987654322",
                                "ifsc_code":"SBI434"
                                }
                                }
                            </div>
                        </div>
                    </div>
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h4 class="card-title w-100">
                                <a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                    Document of Bulk Payout Api
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="collapse" data-parent="#accordion" style="">
                            <div class="card-body">
                                [
                                {
                                "amount":"100",
                                "receiver_name":"SDF3",
                                "payment_mode":"bank_account",
                                "payment_channel":{
                                "bank_name":"SBI Bank",
                                "account_number":"9987654322",
                                "ifsc_code":"SBI434"
                                }
                                },
                                {
                                "amount":"100",
                                "receiver_name":"SF12",
                                "payment_mode":"bank_account",
                                "payment_channel":{
                                "bank_name":"SBI Bank",
                                "account_number":"9987654322",
                                "ifsc_code":"SBI434"
                                }
                                }
                                ]
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>
<!-- /.row -->

@push('modal')
<script>
    $(document).ready(function() {

        $("#integrate").click(function(e) {
            e.preventDefault();
            var webhook_id = $('#webhook_id').val();
            var webhook = $('#webhook_url').val();
            var url = "<?= url('retailer/webhook-api') ?>";

            $.ajax({
                data: {
                    'webhook_id': webhook_id,
                    'webhook_url': webhook,
                    "_token": "{{ csrf_token() }}"
                },
                type: "POST",
                url: url,
                dataType: 'json',
                beforeSend: function() {
                    $('#integrate').html('Processing...');
                },
                success: function(res) {
                    //hide loader
                    $('#integrate').html('submit');

                    /*Start Status message*/
                    if (res.status == 'success' || res.status == 'error') {
                        Swal.fire(
                            `${res.status}!`,
                            res.msg,
                            `${res.status}`,
                        )
                    }
                    /*End Status message*/
                }
            });
        });

    })
</script>
@endpush

@endsection