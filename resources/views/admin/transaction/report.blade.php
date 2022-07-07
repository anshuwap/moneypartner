@extends('admin.layouts.app')

@section('content')
@section('page_heading', 'Retailer List')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">

            <div class="card-header">
                <h3 class="card-title">Transaction Report</h3>
                <div class="card-tools">
                    @if(!empty($filter))
                    <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
                    @else
                    <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
                    @endif
                </div>
            </div>

            <!-- /.card-header -->
            <div class="card-body table-responsive py-4 table-sm">

                <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
                    <div class="col-md-12 ml-auto">
                        <form action="{{ url('admin/transaction-report') }}">
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label>Start Data</label>
                                    <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" name="start_date" />
                                </div>

                                <div class="form-group col-md-2">
                                    <label>End Data</label>
                                    <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" name="end_date" id="end-date" />
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


                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                    <a href="{{ url('admin/transaction-report') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

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
                                        <div style="width:120px;">
                                            <p class="mb-2"><strong>Date</strong></p><span class="text-muted">{{$trans->date}}</span>
                                        </div>
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
                                        <!--<th>Action By</th>-->
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