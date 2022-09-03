@extends('admin.layouts.app')
@section('content')
<style>

</style>
<div class="row">
    <div class="col-12 mt-2">
        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col-md-9">
                        <h3 class="card-title">New Transaction</h3>
                    </div>
                    <div class="col-md-3 text-right">

                        @if(!empty($filter))
                        <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
                        @else
                        <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
                        @endif
                        <a href="{{ url('admin/new-transaction-export') }}{{ !empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''}}" class="btn btn-sm btn-success mr-2"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export</a>
                        <!-- <a href="{{ url('admin/a-refund-export-split') }}{{ !empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''}}" class="btn btn-sm btn-success mr-2"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export Split</a> -->
                    </div>
                </div>
            </div>

            <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
                <div class="col-md-12 ml-auto">
                    <form action="{{ url('admin/new-transaction') }}">
                        <div class="form-row">

                            <div class="form-group col-md-2">
                                <label>Start Data</label>
                                <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" name="start_date" />
                                <!-- <input type="text" class="form-control form-control-sm" value="<?= !empty($filter['date_range']) ? $filter['date_range'] : '' ?>" name="date_range" id="daterange-btn" /> -->
                            </div>

                            <div class="form-group col-md-2">
                                <label>End Data</label>
                                <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" name="end_date" id="end-date" />
                            </div>

                            <div class="form-group col-md-2">
                                <label>Transaction Id</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Transaction ID" value="<?= !empty($filter['transaction_id']) ? $filter['transaction_id'] : '' ?>" name="transaction_id" id="transaction_id" />
                            </div>

                            <div class="form-group col-md-2">
                                <label>Account No</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Account No" value="<?= !empty($filter['account_no']) ? $filter['account_no'] : '' ?>" name="account_no" id="account_no" />
                            </div>

                            <div class="form-group col-md-2">
                                <label>Outlet Name</label>
                                <select class="form-control-sm form-control" name="outlet_id">
                                    <option value="">All</option>
                                    @foreach($outlets as $outlet)
                                    <option value="{{$outlet->_id}}" {{ (!empty($filter['outlet_id']) && $filter['outlet_id'] == $outlet->_id)?"selected":""}}>{{ ucwords($outlet->outlet_name)}}</option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label>Type</label>
                                <select class="form-control form-control-sm" name="type">
                                    <option value="">All</option>dmt_transfer
                                    <option value="dmt_transfer" <?= (!empty($filter['type']) && $filter['type'] == 'dmt_transfer') ? 'selected' : '' ?>>DMT Transfer</option>
                                    <option value="payout" <?= (!empty($filter['type']) && $filter['type'] == 'payout') ? 'selected' : '' ?>>Payput</option>
                                    <option value="payout_api" <?= (!empty($filter['type']) && $filter['type'] == 'payout_api') ? 'selected' : '' ?>>Payout Api</option>
                                    <option value="bulk_payout" <?= (!empty($filter['type']) && $filter['type'] == 'bulk_payout') ? 'selected' : '' ?>>Bulk Payout</option>
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label>Channel</label>
                                <select class="form-control-sm form-control" name="channel">
                                    <option value="" {{ (!empty($filter['channel']) && $filter['channel'] == 'all')?"selected":""}}>All</option>
                                    @foreach($payment_channel as $channel)
                                    <option value="{{$channel->name}}" {{ (!empty($filter['channel']) && $filter['channel'] == $channel->name)?"selected":""}}>{{ ucwords($channel->name)}}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group col-md-2">
                                <label>Status</label>
                                <select class="form-control form-control-sm" name="status">
                                    <option value="">All</option>
                                    <option value="success" <?= (!empty($filter['status']) && $filter['status'] == 'success') ? 'selected' : '' ?>>Success</option>
                                    <!-- <option value="pending" <?= (!empty($filter['status']) && $filter['status'] == 'pending') ? 'selected' : '' ?>>Pending</option> -->
                                    <option value="process" <?= (!empty($filter['status']) && $filter['status'] == 'process') ? 'selected' : '' ?>>Process</option>
                                    <option value="rejected" <?= (!empty($filter['status']) && $filter['status'] == 'rejected') ? 'selected' : '' ?>>Rejected</option>
                                    <option value="refund_pending" <?= (!empty($filter['status']) && $filter['status'] == 'refund_pending') ? 'selected' : '' ?>>Refund Pending</option>
                                    <option value="refund" <?= (!empty($filter['status']) && $filter['status'] == 'refund') ? 'selected' : '' ?>>Refund</option>
                                    <option value="failed" <?= (!empty($filter['status']) && $filter['status'] == 'failed') ? 'selected' : '' ?>>Failed</option>
                                    <option value="process" <?= (!empty($filter['status']) && $filter['status'] == 'process') ? 'selected' : '' ?>>Process</option>
                                </select>
                            </div>
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                <a href="{{ url('admin/new-transaction') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- /.card-header -->
            <div class="card-body table-responsive py-2 table-sm">
                <table id="table" class="table table-hover text-nowrap table-sm">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Outlet</th>
                            <th>Channel</th>
                            <th>Amount</th>
                            <th>Fees</th>
                            <th>Beneficiary</th>
                            <th>IFSC</th>
                            <th>Account No.</th>
                            <th>UTR No.</th>
                            <th>Status</th>
                            <th>Request Date</th>
                            <th>Action By</th>
                            <th>Action Date</th>
                            <th>IP Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction as $key=>$trans)
                        <?php

                        $UserName = !empty($trans->UserName['full_name']) ? 'Action By- ' . $trans->UserName['full_name'] : '';

                        $payment = (object)$trans->payment_channel;
                        $comment = !empty($trans->response['msg']) ? $trans->response['msg'] : '';
                        $type = (!empty($trans->response['payment_mode'])) ? $trans->response['payment_mode'] : '';

                        if ($trans->status == 'success') {
                            $status = '<span class="tag-small"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                            $action = '';
                            $checkbox = '';
                        } else if ($trans->status == 'refund') {
                            $status = '<span class="tag-small"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                            $action = '-';
                            $checkbox = '-';
                        } else if ($trans->status == 'rejected') {
                            $status = '<span class="tag-small-danger"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                            $action = '';
                            $checkbox  = '';
                        } else if ($trans->status == 'process') {
                            $status = '<span class="tag-small-purple"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                            $action = '<a href="javascript:void(0);" class="btn btn-secondary btn-xs payment_status" type="' .  $type . '"  _id="' . $trans->_id . '"><i class="fas fa-check-double"></i>&nbsp;Status</a>';
                            $checkbox  = '';
                        } else if ($trans->status == 'failed') {
                            $status = '<span class="tag-small-danger"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                            $action = '<a href="javascript:void(0);" payment_mode="' . $trans->payment_mode . '" class="btn btn-danger btn-xs retailer_trans" _id="' . $trans->_id . '"><i class="fas fa-radiation-alt"></i>&nbsp;Action</a>';
                            $checkbox  = '<input type="checkbox" class="select_me checkbox" value="' . $trans->_id . '" />';
                        } else if ($trans->status == 'refund_pending') {
                            $status = '<span class="tag-small-meganta"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords(str_replace('_', ' ', $trans->status)) . '</a></span>';
                            $action = '';
                            $checkbox  = '';
                        } else {
                            $status = '<span class="tag-small-warning"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                            $action = '<a href="javascript:void(0);" payment_mode="' . $trans->payment_mode . '" class="btn btn-danger btn-xs retailer_trans" _id="' . $trans->_id . '"><i class="fas fa-radiation-alt"></i>&nbsp;Action</a>';
                            $checkbox = ' <input type="checkbox" class="select_me checkbox" value="' . $trans->_id . '" />';
                        } ?>

                        <tr>

                            <td> <span data-toggle="tooltip" data-placement="bottom" title="{{$UserName}}">{{++$key}}</span></td>
                            <td>
                                <span data-toggle="tooltip" data-placement="bottom" title="{{ $trans->transaction_id }}"> {{ (!empty($trans->OutletName['outlet_name']))?$trans->OutletName['outlet_name']:'-';}}</span>
                            </td>
                            <td><?= (!empty($trans->response['payment_mode'])) ? $trans->response['payment_mode'] : '-' ?></td>

                            <td>{!! mSign($trans->amount) !!}</td>
                            <td>{!! mSign($trans->transaction_fees) !!}</td>
                            <td>{{ ucwords($trans->receiver_name)}}</td>
                            <!-- <td>{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</td> -->
                            <td><span data-toggle="tooltip" data-placement="bottom" title="<?= (!empty($payment->bank_name)) ? $payment->bank_name : '' ?>">{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</span></td>
                            <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
                                <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
                            </td>
                            <!-- <td><?= (!empty($payment->bank_name)) ? $payment->bank_name : '-' ?></td> -->
                            <td> <?= (!empty($trans->response['utr_number'])) ? $trans->response['utr_number'] : '-' ?></td>
                            <td>{!! $status !!}</td>
                            <td>{{ !empty($trans->split_created)?date('d,M y H:i',$trans->split_created):date('d,M y H:i',$trans->created) }}</td>
                            <td>{{ !empty($trans->UserName['full_name']) ?$trans->UserName['full_name'] : '';}}</td>
                            <td><?php $actionM = !(empty($trans->response['action'])) ? $trans->response['action'] : '';
                                echo !empty($trans->response['action_date']) ? '<span data-toggle="tooltip" data-placement="bottom" title="' . $actionM . '">' . date('d,M y H:i', $trans->response['action_date']) . '</span>' : '' ?></td>
                            <td>{{$trans->ip_address}}</td>
                            <td> <a href="javascript:void(0);" class="btn btn-info btn-xs view_dashboard" _id="{{ $trans->_id }}"><i class="fas fa-eye"></i>&nbsp;view</a>
                                {!! $action !!}</td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
                {{ $transaction->appends($_GET)->links()}}
            </div>
            <!-- /.card-body -->

        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->

<!--start retailer transfer module-->
@push('modal')

<!-- Modal -->
<div class="modal fade" id="approve_modal_dashboard" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_bank_dashboard">Success/Reject Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body">
                <form id="approve_trans_dashboard" action="{{ url('admin/a-transaction') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="trans_id_dahboard" name="trans_id">
                            <input type="hidden" id="key_dashboard" name="key">

                            <div class="form-group" id="type-m">
                                <label>Select</label>
                                <select name="type" required class="form-control form-control-sm" id="type">
                                    <option value="">Select</option>
                                    <option value="api">Api</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>

                            <div id="action">
                            </div>

                            <div id="success_dashboard"></div>

                            <div class="form-group" id="comment-field_dashboard" style="display: none;">
                                <label>Comment</label>
                                <select name=response[msg] class="form-control form-control-sm" id="comment_dashboard">

                                </select>
                                <span id="comment_msg" class="custom-text-danger"></span>
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

<!-- Modal -->
<div class="modal fade" id="view_modal_dashboard" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="heading_bank_dashboard">Account Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" id="details1_dashboard">
                <div id="details_dashboard"></div>

                <div class="row">
                    <div class="col-md-12">
                        <label>Change Payment Channel</label>
                        <input type="hidden" value="" id="view-id" name="id">
                        <div class="input-group input-group-sm">
                            <select name="response[payment_mode]" class="form-control form-control-sm" id="channel">
                                <option value="">Select</option>
                                <?php foreach ($payment_channel as $channel) {
                                    echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                                } ?>
                            </select>
                            <span class="input-group-append ">
                                <button type="button" class="btn btn-info btn-flat" id="change-channel">Change</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="approve_modal_action" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_bank_dashboard">Success/Reject Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body">
                <form id="approve_trans_action" action="{{ url('admin/change-transaction-status') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="trans_id_action" name="trans_id">
                            <input type="hidden" id="key_dashboard" name="key">

                            <div class="form-group">
                                <label>Action</label>
                                <select name="status" id="status-select-action" class="status-select-action form-control form-control-sm">
                                    <option value="">Select</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                <span id="status_msg" class="custom-text-danger"></span>
                            </div>

                            <div id="action_action">
                            </div>

                            <div id="success_action"></div>

                            <div class="form-group" id="comment-field_action" style="display: none;">
                                <label>Comment</label>
                                <select name=response[msg] class="form-control form-control-sm" id="comment_action">

                                </select>
                                <span id="comment_msg" class="custom-text-danger"></span>
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
    $('#change-channel').click(function() {
        var channel = $('#channel').val();
        var id = $('#view-id').val();
        $.ajax({
            url: "<?= url('admin/change-new-channel') ?>",
            data: {
                'id': id,
                'channel': channel
            },
            type: 'GET',
            dataType: "json",
            success: function(res) {
                /*Start Status message*/
                if (res.status == 'success' || res.status == 'error') {
                    Swal.fire(
                        `${res.status}!`,
                        res.msg,
                        `${res.status}`,
                    );
                    setTimeout(function() {
                        location.reload();
                    });
                }
                /*End Status message*/
            }
        })
    });

    $(document).on('click', '.retailer_trans', function(e) {
        e.preventDefault();
        $('#trans_id_dahboard').val($(this).attr('_id'));
        $('#key_dashboard').val(key_dashboard);

        var payment_mode = $(this).attr('payment_mode');
        if (payment_mode == 'upi') {
            $('#type-m').hide();
            $('#success_dashboard').html(``);
            upi();
        } else {
            $('#type-m').show();
            $('#success_dashboard').html(``);
            $('#action').html(``);
        }
        $('#view_modal_dashboard').modal('hide');
        $('#approve_modal_dashboard').modal('show');
    })

    $(document).on('click', '.utrupdate', function() {
        $('#utr').toggle();
    })

    //check payment status
    $(document).on('click', '#update_utr', function() {
        var id = $('#trnsaction-id').val();
        var select = $(this);
        var utr = $('#utr_no').val();
        // alert(type);

        $.ajax({
            data: {
                'utr': utr,
                'id': id
            },
            type: "GET",
            url: '{{ url("admin/update-new-utr") }}',
            dataType: 'json',
            beforeSend: function() {
                $(select).html('<span class="spinner-grow spinner-grow-sm" style="width: 0.75rem;height: 0.75rem;"></span>&nbsp;Loading..');
            },
            success: function(res) {
                //hide loader

                //for reset all field
                if (res.status == 'success') {
                    $(select).html('<i class="fas fa-check-double"></i>&nbsp;Done');
                    setTimeout(function() {
                        location.reload();
                    }, 1000)
                }

                /*Start Status message*/
                if (res.status == 'error') {
                    $(select).html('<i class="fas fa-times"></i>&nbsp;Failed');
                    Swal.fire(
                        `${res.status}!`,
                        res.msg,
                        `${res.status}`,
                    )
                }
                /*End Status message*/
            }
        });
    })


    $('#type').change(() => {
        let status = $('#type').val();
        if (status == 'manual') {
            $('#approve_trans_dashboard').attr('action', '{{url("admin/a-transaction")}}');
            $('#action').html(` <div class="form-group">
                                   <label>Action</label>
                                   <select name="status" id="status-select-dashboard" required class="status-select-dashboard form-control form-control-sm">
                                       <option value="">Select</option>
                                       <option value="success">Success</option>
                                       <option value="pending">Pending</option>
                                       <option value="rejected">Rejected</option>
                                        <option value="refund_pending">Refund Pending</option>
                                   </select>
                                   <span id="status_msg" class="custom-text-danger"></span>
                               </div>`);
        } else if (status == 'api') {
            $('#approve_trans_dashboard').attr('action', '{{url("admin/a-store-api")}}');
            $('#action').html(`<div class="form-group">
               <select class="form-control form-control-sm" name="api" id="api" required>
               <option value=''>Select</option>
               <option value="payunie_preet_kumar">Payunie - PREET KUMAR</option>
               <option value="payunie_rashid_ali">Payunie -Rashid Ali</option>
               <option value="pay2all">Pay2ALL - PRAVEEN</option>
               <option value="odnimo">Odnimo</option>
               <option value="clickncash">ClicknCash</option>
               </select>
               </div>`);
        }
    })

    $(document).on('change', '#status-select-dashboard', function() {
        let status = $('#status-select-dashboard').val();

        if (status == 'success') {
            $('#challel').html(``);
            $('#success_dashboard').html(`<div class="form-group">
                   <label>Select Payment Channel</label>
                   <select name="response[payment_mode]" required class="form-control form-control-sm" id="payment_channel">
                     <option value="">Select</option>
                     <?php foreach ($payment_channel as $channel) {
                            echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                        } ?>
                   </select>
                   <span id="payment_channel_msg" class="custom-text-danger"></span>
                 </div>
                 <div class="form-group">
                                <label>UTR/Transaction</label>
                                <input type="text" placeholder="UTR/Transaction" required id="utr" name="response[utr_number]" class="form-control form-control-sm">
                                <span id="utr_transaction_msg" class="custom-text-danger"></span>
                            </div>`);
        } else if (status == 'rejected' || status == 'refund_pending') {
            $('#challel').html(``);
            $('#success_dashboard').html(``);
        } else {
            $('#challel').html(``);
            $('#success_dashboard').html(`<div class="form-group">
                   <label>Select Payment Channel</label>
                   <select name="response[payment_mode]" required class="form-control form-control-sm" id="payment_channel">
                     <option value="">Select</option>
                     <?php foreach ($payment_channel as $channel) {
                            echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                        } ?>
                   </select>
                   <span id="payment_channel_msg" class="custom-text-danger"></span>
                 </div>`);
        }
    })

    //show transaction detils
    $(document).on('click', '.view_dashboard', function() {
        var _id = $(this).attr('_id');
        $.ajax({
            url: "<?= url('admin/a-trans-detail') ?>",
            data: {
                'id': _id,
            },
            type: 'GET',
            dataType: "json",
            success: function(res) {

                $('#details_dashboard').html(res.table);
                $('#view-id').val(res.id);
                $('#channel').val(res.channel);
                $('#view_modal_dashboard').modal('show');
            }
        })
    });

    $(document).on('change', '.status-select-dashboard', function(e) {
        e.preventDefault();

        var type = $(this).val();

        if (type == '') {
            $('#comment-field_dashboard').hide();
            $('#comment_field_dashboard1').show();
        } else {
            $.ajax({
                url: "<?= url('admin/a-trans-comment') ?>",
                data: {
                    'type': type
                },
                type: 'GET',
                dataType: "json",
                success: function(res) {
                    $('#comment-field_dashboard').show();
                    $('#comment_field_dashboard1').show();
                    $('#comment_dashboard').html(res);
                    $('#comment_dashboard1').html(res);

                }
            })
        }
    })

    /*start form submit functionality*/
    $("form#approve_trans_dashboard").submit(function(e) {
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
                    $('form#approve_trans_dashboard')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 1000)
                }
            }
        });
    });

    /*end form submit functionality*/

    function copyToClipboard(element, copy) {
        var $temp = $("<input />");
        $("#details1_dashboard").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $(copy).removeClass('d-none');
        $temp.remove();
    }

    //check payment status
    $('.payment_status').click(function() {
        var id = $(this).attr('_id');
        var select = $(this);
        var type = $(this).attr('type');
        // alert(type);

        $.ajax({
            data: {
                'type': type,
                '_id': id
            },
            type: "GET",
            url: '{{ url("admin/payment-status") }}',
            dataType: 'json',
            beforeSend: function() {
                $(select).html('<span class="spinner-grow spinner-grow-sm" style="width: 0.75rem;height: 0.75rem;"></span>&nbsp;Loading..');
            },
            success: function(res) {
                //hide loader

                //for reset all field
                if (res.status == 'success') {
                    $(select).html('<i class="fas fa-check-double"></i>&nbsp;Done');
                    setTimeout(function() {
                        location.reload();
                    }, 1000)
                }

                /*Start Status message*/
                if (res.status == 'error') {
                    $(select).html('<i class="fas fa-times"></i>&nbsp;Failed');
                    Swal.fire(
                        `${res.status}!`,
                        res.msg,
                        `${res.status}`,
                    )
                }
                /*End Status message*/
            }
        });
    })

    /*start bulk Approve transaction*/
    $('#checkAll').click(function() {

        $('.table input:checkbox').prop('checked', this.checked);
        if ($(".table input[type=checkbox]:checked").length > 1) {

            $('#bluckAssignBtn').prop('disabled', false);
            $('#bluckAssignBlock').removeAttr('style');
        } else {
            $('#bluckAssignBtn').prop('disabled', true);
            $('#bluckAssignBlock').css({
                'pointer-events': 'none !important;'
            });
        }
    });
    $('.checkbox').click(function() {
        if ($(".table input[type=checkbox]:checked").length > 0) {
            $('#bluckAssignBtn').prop('disabled', false);
            $('#bluckAssignBlock').removeAttr('style');
        } else {
            $('#bluckAssignBtn').prop('disabled', true);
            $('#bluckAssignBlock').css({
                'pointer-events': 'none !important;'
            });
        }
    });

    $('#bluckAssignBtn').click(function() {
        $('#approve_trans_')[0].reset();
        var transID = [];
        $(".table input[type=checkbox]:checked").each(function(i) {
            if ($(this).val() != 'on')
                transID.push($(this).val());
        });
        $('#trans_id_dahboard1').val(transID);
        $('#bluckAssignBtn1').modal('show');
    })
    /*end bulk approve transaction*/


    //for success case functionality
    $(document).on('click', '.success-action', function(e) {
        e.preventDefault();
        $('#trans_id_action').val($(this).attr('_id'));
        $('#view_modal_dashboard').modal('hide');
        $('#approve_modal_action').modal('show');
    })

    $(document).on('change', '#status-select-action', function() {
        let status = $('#status-select-action').val();
        if (status == 'rejected') {
            $('#challel').html(``);
            $('#success_action').html(``);
        } else {
            $('#challel').html(``);
            $('#success_action').html(`<div class="form-group">
                   <label>Select Payment Channel</label>
                   <select name="response[payment_mode]" class="form-control form-control-sm" id="payment_channel">
                     <option value="">Select</option>
                     <?php foreach ($payment_channel as $channel) {
                            echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                        } ?>
                   </select>
                   <span id="payment_channel_msg" class="custom-text-danger"></span>
                 </div>`);
        }
    });


    $(document).on('change', '.status-select-action', function(e) {
        e.preventDefault();

        var type = $(this).val();

        if (type == '') {
            $('#comment_action').hide();
            $('#comment-field_action').show();
        } else {
            $.ajax({
                url: "<?= url('admin/a-trans-comment') ?>",
                data: {
                    'type': type
                },
                type: 'GET',
                dataType: "json",
                success: function(res) {
                    // $('#comment_action').show();
                    $('#comment-field_action').show();
                    $('#comment_action').html(res);
                    $('#comment_dashboard1').html(res);

                }
            })
        }
    })

    /*start form submit functionality*/
    $("form#approve_trans_action").submit(function(e) {
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
                    $('form#approve_trans_action')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 1000)
                }
            }
        });
    });

    /*end form submit functionality*/
</script>



<div class="modal fade" id="bluckAssignBtn1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_bank_dashboard">Success/Reject Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body">
                <form id="approve_trans_" action="{{ url('admin/bulk-action') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="trans_id_dahboard1" name="trans_id">

                            <div class="form-group">
                                <label>Select Api</label>
                                <select class="form-control form-control-sm" name="api" id="api" required>
                                    <option value=''>Select</option>
                                    <option value="payunie_preet_kumar">Payunie - PREET KUMAR</option>
                                    <option value="payunie_rashid_ali">Payunie -Rashid Ali</option>
                                    <option value="pay2all">Pay2ALL - PRAVEEN</option>
                                    <option value="odnimo">Odnimo</option>
                                    <option value="clickncash">ClicknCash</option>
                                </select>
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


<!-- Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" id="preview-modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Preview Transaction</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- for loader -->
            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body pl-2 pr-2">
                <div class="d-none" id="show-pin">
                    <input type="hidden" id="no_of_record">
                    <input type="hidden" id="total_amount">
                    <input type="hidden" id="api_val">
                    <div id="preview-import-data">
                    </div>

                    <div class="form-group text-center">
                        <button type="button" id="paied" class="btn btn-success btn-sm"><i class="fas fa-compress-arrows-alt"></i>&nbsp;Send</button>
                        <button type="button" class="btn btn-sm btn-success" data-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>&nbsp;Close
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<script>
    /*start form submit functionality*/
    $("form#approve_trans_").submit(function(e) {
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

                if (res.status == 'preview') {
                    $('#import-file').addClass('d-none');
                    $('#preview-modal').addClass('modal-lg-custom');
                    //   $('#preview-modal').removeClass('modal-dialog');
                    $('#show-pin').removeClass('d-none');
                    $('#preview-import-data').html(res.data.table_data);
                    $('#no_of_record').val(res.data.no_of_record);
                    $('#total_amount').val(res.data.total_amount);
                    $('#api_val').val(res.api);
                    $('#previewModal').modal('show')
                }

                //for reset all field
                if (res.status == 'success') {
                    $('form#approve_trans_')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 1000)
                }
            }
        });
    });

    $('#paied').click(function(e) {
        e.preventDefault();

        var no_of_record = $('#no_of_record').val();
        var total_amount = $('#total_amount').val();
        Swal.fire({
            title: '<h6>Number Of Record&nbsp;-<b>' + no_of_record + '</b></h6><h6>Total Amount &nbsp;&nbsp;<b> &#8377;' + total_amount + '</b></h6>',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Confirm',
            denyButtonText: `Cancel`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                // Swal.fire('Saved!', '', 'success')
                $('#hide-pin').hide();
                $('#bluckAssignBtn1').modal('hide');
                importSequence(0);
            } else if (result.isDenied) {
                Swal.fire('Changes are not saved', '', 'info')
            }
        })
    })

    function importSequence(index) {
        var api = $('#api_val').val();
        var url1 = '{{ url("admin/payToApi")}}';
        $.ajax({
            data: {
                'api': api,
                'index': index
            },
            type: "GET",
            url: url1,
            dataType: 'json',
            success: function(res) {
                if (index == 0)
                    $('.preview-table').remove();

                $('#preview-table').append(res.data);

                if (index + 1 != res.all_row) {
                    importSequence(res.index);
                } else {
                    $('#paied').remove();
                }
                $('#paied').remove();
            }
        });
    }
    $('#previewModal').on('hidden.bs.modal', function() {
        location.reload();
    });
</script>

@endpush
<!--end retailer transer module-->
@endsection

@include('admin.transaction.splitTransaction2')