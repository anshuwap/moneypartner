@extends('retailer.layouts.app')

@section('content')
@section('page_heading', 'Retailer List')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">

            <div class="card-header">
                <h3 class="card-title">Transaction Report</h3>
            </div>

            <!-- /.card-header -->
            <div class="card-body table-responsive py-4 table-sm">


                <div id="accordion">

                    @php
                    $i = 1;
                    @endphp
                    @foreach($trnasReport as $key=>$trans)
                    @php $trans = (object)$trans;
                    @endphp
                    <div class="card">
                        <div class="card-header" id="heading{{$key}}">
                            <h5 class="mb-0">
                                <a href="javascript:void(0);" class="text-dark" data-toggle="collapse" data-target="#collapse{{$key}}" aria-expanded="true" aria-controls="collapse{{$key}}" style="font-size: 14px;">

                                    <div class="d-flex justify-content-between">
                                        <div style="width:60px;"><strong>#</strong>{{$i++}}</div>
                                        <div style="width:200px;">
                                            <p class="mb-2">All Payout</p><span class="text-muted">{!!mSign($trans->total_amount)!!} || {{$trans->total_count}}</span>
                                        </div>
                                        <div style="width:200px;">
                                            <p class="mb-2">Approved Payout</p><span class="text-muted">{!!mSign($trans->success_amount)!!} || {{$trans->success_count}}</span>
                                        </div>
                                        <div style="width:200px;">
                                            <p class="mb-2">Failed Payout</p><span class="text-muted">{!!mSign($trans->failed_amount)!!} || {{$trans->failed_count}}</span>
                                        </div>
                                        <div style="width:200px;">
                                            <p class="mb-2">Refund Payout</p><span class="text-muted">{!!mSign($trans->refund_amount)!!} || {{$trans->refund_count}}</span>
                                        </div>
                                        <div style="width:200px;">
                                            <p class="mb-2">Pending Payout</p><span class="text-muted">{!!mSign($trans->pending_amount)!!} || {{$trans->pending_count}}</span>
                                        </div>
                                        <div style="width:200px;">
                                            <p class="mb-2">Rejected Payout</p><span class="text-muted">{!!mSign($trans->rejected_amount)!!} || {{$trans->rejected_count}}</span>
                                        </div>
                                        <div style="width:100px;"><p class="mb-2"><strong>Date</strong></p><span class="text-muted">{{$trans->date}}</span></div>
                                    </div>
                                </a>
                            </h5>
                        </div>

                        <div id="collapse{{$key}}" class="collapse " aria-labelledby="heading{{$key}}" data-parent="#accordion">
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <th>Sr No.</th>
                                        <th>Transaction Id</th>
                                        <th>Channel</th>
                                        <th>Amount</th>
                                        <th>Beneficiary</th>
                                        <th>IFSC</th>
                                        <th>Account No.</th>
                                        <th>UTR No.</th>
                                        <th>Status</th>
                                        <th>Request Date</th>
                                        <th>Action By</th>
                                        <th>Action Date</th>
                                    </tr>

                                    @foreach($trans->transactions as $ikey=>$val)
                                    @php
                                    $val = (object)$val;
                                    @endphp
                                    <?php

                                    $payment = (object)$val->payment_channel;
                                    $comment = !empty($val->response['msg']) ? $val->response['msg'] : '';

                                    if ($val->status == 'success') {
                                        $status = '<span class="tag-small"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($val->status) . '</a></span>';
                                    } else if ($val->status == 'refund') {
                                        $status = '<span class="tag-small"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($val->status) . '</a></span>';
                                    } else if ($val->status == 'rejected') {
                                        $status = '<span class="tag-small-danger"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($val->status) . '</a></span>';
                                    } else if ($val->status == 'process') {
                                        $status = '<span class="tag-small-purple"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($val->status) . '</a></span>';
                                    } else if ($val->status == 'failed') {
                                        $status = '<span class="tag-small-danger"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($val->status) . '</a></span>';
                                    } else if ($val->status == 'refund_pending') {
                                        $status = '<span class="tag-small-meganta"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords(str_replace('_', ' ', $val->status)) . '</a></span>';
                                    } else if (!empty($val->response)) {
                                        $status = '<span class="tag-small-warning"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">Pending</a></span>';
                                    } else {
                                        $status = '<span class="tag-small"><a href="javascript:void(0)" class="text-dark mt-1" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">Success</a></span>';
                                    }

                                    ?>
                                    <tr>
                                        <td>{{ ++$ikey }}</td>
                                        <td style="width: 100px;">
                                            {{ $val->transaction_id }}
                                        </td>
                                        <td><?= (!empty($val->response['payment_mode'])) ? $val->response['payment_mode'] : '-' ?></td>
                                        <td>{!! mSign($val->amount) !!}</td>
                                        <td>{{ ucwords($val->receiver_name)}}</td>
                                        <td><span data-toggle="tooltip" data-placement="bottom" title="<?= (!empty($payment->bank_name)) ? $payment->bank_name : '' ?>">{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</span></td>
                                        <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
                                            <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
                                        </td>
                                        <td> <?= (!empty($val->response['utr_number'])) ? $val->response['utr_number'] : '-' ?></td>
                                        <td>{!! $status !!}</td>
                                        <td>{{ $val->created }}</td>
                                        <td>{{ !empty($val->UserName['full_name']) ?$val->UserName['full_name'] : '';}}</td>
                                        <td><?php $actionM = !(empty($val->response['action'])) ? $val->response['action'] : '';
                                            echo !empty($val->response['action_date']) ? '<span data-toggle="tooltip" data-placement="bottom" title="' . $actionM . '">' . date('d,M y H:i', $val->response['action_date']) . '</span>' : '' ?></td>

                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- $transactions->appends($_GET)->links() -->
            </div>
            <!-- /.card-body -->

        </div>
        <!-- /.card -->
    </div>
</div>

@endsection