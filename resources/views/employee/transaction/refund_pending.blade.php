@extends('employee.layouts.app')
@section('content')
<style>

</style>
<div class="row">
    <div class="col-12 mt-2">
        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col-md-9">
                        <h3 class="card-title">Refund Pending Transaction</h3>
                    </div>
                    <div class="col-md-3 text-right">

                            @if(!empty($filter))
                            <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
                            @else
                            <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
                            @endif
                            <a href="{{ url('employee/refund-pending-export') }}{{ !empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''}}" class="btn btn-sm btn-success mr-2"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export</a>

                    </div>
                </div>
            </div>

            <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
                <div class="col-md-12 ml-auto">
                    <form action="{{ url('employee/refund-pending') }}">
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

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                <a href="{{ url('employee/refund-pending') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
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
                          <!--<th>Channel</th>-->
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
                        if ($trans->status == 'refund_pending') {
                            $status = '<span class="tag-small-meganta"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords(str_replace('_', ' ', $trans->status)) . '</a></span>';
                            $action = '';
                            $checkbox  = '';
                        } ?>
                        <tr>

                            <td> <span data-toggle="tooltip" data-placement="bottom" title="{{$UserName}}">{{++$key}}</span></td>
                            <td>
                                <span data-toggle="tooltip" data-placement="bottom" title="{{ $trans->transaction_id }}"> {{ (!empty($trans->OutletName['outlet_name']))?$trans->OutletName['outlet_name']:'-';}}</span>
                            </td>
                            <!--<td>{{ (!empty($trans->response['payment_mode'])) ? $trans->response['payment_mode'] : '-' }}</td>-->

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

<script>
    $('#change-channel').click(function() {
        var channel = $('#channel').val();
        var id = $('#view-id').val();
        $.ajax({
            url: "<?= url('employee/change-channel') ?>",
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
                    )
                }
                /*End Status message*/
            }
        })
    });

    //show transaction detils
    $(document).on('click', '.view_dashboard', function() {
        var _id = $(this).attr('_id');
        $.ajax({
            url: "<?= url('employee/a-trans-detail') ?>",
            data: {
                'id': _id,
            },
            type: 'GET',
            dataType: "json",
            success: function(res) {

                $('#details_dashboard').html(res.table);
                $('#view-id').val(res.id);
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
                url: "<?= url('employee/a-trans-comment') ?>",
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
<!--end retailer transer module-->
@endsection

@include('employee.transaction.splitTransaction2')