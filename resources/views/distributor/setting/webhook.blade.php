@extends('distributor.layouts.app')
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
                            <div class="ribbon btn-success">
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


                        <small>Integrate Your Base URL Here for authenticate valid Api Source.</small>
                        <div class="form-group mr-4">
                            @if(!empty($base_url))
                            <input type="hidden" id="base_url_id" value="{{ $base_url->_id }}" />
                            @endif

                            <div class="input-group input-group-sm">

                                <input type="text" value="{{ (!empty($base_url->base_url))?$base_url->base_url:''}}" class="form-control form-control form-control-sm" placeholder="Enter Base URL" name="base_url" id="base_url">
                                <span class="input-group-append ">
                                    <button type="button" class="btn btn-info btn-flat" id="base-url-integrate">submit</button>
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
                <div><b>Api Documentation (Postman) :</b>
                    <span>
                        <a href="https://documenter.getpostman.com/view/18356665/UVsMtkKY#f427155c-5262-43b0-a799-6c608feca074" target="_blank" id="textl">https://documenter.getpostman.com/view/18356665/UVsMtkKY#f427155c-5262-43b0-a799-6c608feca074</a>
                    </span>
                      &nbsp;&nbsp;<span><a href="javascript:void(0);" onClick="copyToClipboard('#textl','#copyl')" class="text-success"><i class="fas fa-copy"></i></a></span>
                      <span class="ml-2 d-none" id="copyl"><i class="fas fa-check-circle text-success"></i>Copied</span>

                </div>

                <div><b>Postman Api Collection URL (JSON) :</b>
                    <span class="text-danger" id="textc">
                       https://www.getpostman.com/collections/720dbf608d51665a1d07
                    </span>
                   &nbsp;&nbsp; <span><a href="javascript:void(0);" onClick="copyToClipboard('#textc','#copyc')" class="text-success"><i class="fas fa-copy"></i></a></span>
                      <span class="ml-2 d-none" id="copyc"><i class="fas fa-check-circle text-success"></i>Copied</span>
                </div>


            </div>

        </div>

    </div>
</div>
<!-- /.row -->
<div id="details1_dashboard">

</div>
@push('modal')
<script>
    $(document).ready(function() {

        $("#integrate").click(function(e) {
            e.preventDefault();
            var webhook_id = $('#webhook_id').val();
            var webhook = $('#webhook_url').val();
            var url = "<?= url('distributor/webhook-api') ?>";

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


    $("#base-url-integrate").click(function(e) {
        e.preventDefault();
        var base_url_id = $('#base_url_id').val();
        var base_url = $('#base_url').val();
        var url = "<?= url('distributor/base-url-api') ?>";

        $.ajax({
            data: {
                'base_url_id': base_url_id,
                'base_url': base_url,
                "_token": "{{ csrf_token() }}"
            },
            type: "POST",
            url: url,
            dataType: 'json',
            beforeSend: function() {
                $('#base-url-integrate').html('Processing...');
            },
            success: function(res) {
                //hide loader
                $('#base-url-integrate').html('submit');

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

     function copyToClipboard(element, copy) {
    var $temp = $("<input />");
    $("#details1_dashboard").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $(copy).removeClass('d-none');
    $temp.remove();
  }
</script>
@endpush

@endsection