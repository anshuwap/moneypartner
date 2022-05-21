@extends('retailer.layouts.app')

@section('content')
@section('page_heading', 'Retailer List')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">

            <div class="card-header">
                <h3 class="card-title">Refund Pending List</h3>
                <div class="card-tools">
                    @if(!empty(MoneyPartnerOption()->dmt_transfer) && MoneyPartnerOption()->dmt_transfer ==1)
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" id="create_customer"><i class="fas fa-plus-circle"></i>&nbsp;Add DMT</a>
                    @endif
                    @if(!empty(MoneyPartnerOption()->payout) && MoneyPartnerOption()->payout ==1)
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" id="create_payout"><i class="fas fa-plus-circle"></i>&nbsp;Add Payout</a>
                    @endif

                    @if(!empty(MoneyPartnerOption()->bulk_payout) && MoneyPartnerOption()->bulk_payout ==1)
                    <a href="javascript:void(0);" id="import" class="btn btn-sm btn-success"><i class="fas fa-cloud-upload-alt"></i>&nbsp;Bulk Upload</a>
                    @endif

                    <a href="{{ url('retailer/refund-pending-export') }}{{ !empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''}}" class="btn btn-sm btn-success"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export</a>
                    @if(!empty($filter))
                    <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
                    @else
                    <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
                    @endif
                </div>
            </div>


            <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
                <div class="col-md-12 ml-auto">
                    <form action="{{ url('retailer/refund-pending') }}">
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
                                <label>Banficiary</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Enter Banficiary Name" value="<?= !empty($filter['banficiary']) ? $filter['banficiary'] : '' ?>" name="banficiary" id="banficiary" />
                            </div>

                            <div class="form-group col-md-2">
                                <label>Type</label>
                                <select class="form-control form-control-sm" name="type">
                                    <option value="">All</option>
                                    <option value="dmt_transfer" <?= (!empty($filter['type']) && $filter['type'] == 'dmt_transfer') ? 'selected' : '' ?>>DMT Transfer</option>
                                    <option value="payout" <?= (!empty($filter['type']) && $filter['type'] == 'payout') ? 'selected' : '' ?>>Payput</option>
                                    <option value="payout_api" <?= (!empty($filter['type']) && $filter['type'] == 'payout_api') ? 'selected' : '' ?>>Payout Api</option>
                                    <option value="bulk_payout" <?= (!empty($filter['type']) && $filter['type'] == 'bulk_payout') ? 'selected' : '' ?>>Bulk Payout</option>
                                </select>
                            </div>

                            <!-- <div class="form-group col-md-2">
                                     <label>Status</label>
                                     <select class="form-control form-control-sm" name="status">
                                         <option value="">All</option>
                                         <option value="success" <?= (!empty($filter['status']) && $filter['status'] == 'success') ? 'selected' : '' ?>>Success</option>
                                         <option value="pending1" <?= (!empty($filter['status']) && $filter['status'] == 'pending1') ? 'selected' : '' ?>>Pending</option>
                                         <option value="reject" <?= (!empty($filter['status']) && $filter['status'] == 'reject') ? 'selected' : '' ?>>Reject</option>
                                     </select>
                                 </div> -->

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                <a href="{{ url('retailer/refund-pending') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- /.card-header -->
            <div class="card-body table-responsive py-4 table-sm">
                <table id="table" class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <!-- <th>Customer</th> -->
                            <th>Transaction Id</th>
                            <th>Amount</th>
                            <th>Beneficiary</th>
                            <th>IFSC</th>
                            <th>Account No.</th>
                            <th>Status</th>
                            <th>Request Date</th>
                            <th>Action By</th>
                            <th>Action Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $key=>$trans)
                        <?php

                        $payment = (object)$trans->payment_channel;
                        $comment = !empty($trans->response['msg']) ? $trans->response['msg'] : '';

                        $clame = '';
                        $status ='';
                        if ($trans->status == 'refund_pending') {
                            $status = '<span class="tag-small-meganta"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords(str_replace('_', ' ', $trans->status)) . '</a></span>';
                        }
                        $clame = '<a href="javascript:void(0)" trans_id = "' . $trans->_id . '" trans_no="' . $trans->transaction_id . '" amount="' . $trans->amount . '" class="clame ml-2 btn btn-xs btn-danger">Claim</a>';
                        ?>
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td style="width: 100px;">
                                {{ $trans->transaction_id }}
                            </td>
                            <td>{!! mSign($trans->amount) !!}</td>
                            <td>{{ ucwords($trans->receiver_name)}}</td>
                            <td><span data-toggle="tooltip" data-placement="bottom" title="<?= (!empty($payment->bank_name)) ? $payment->bank_name : '' ?>">{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</span></td>
                            <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
                                <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
                            </td>
                            <td>{!! $status !!}</td>
                            <td>{{ date('d M y H:i',$trans->created) }}</td>
                            <td>{{ !empty($trans->UserName['full_name']) ?$trans->UserName['full_name'] : '';}}</td>
                            <td><?php $actionM = !(empty($trans->response['action'])) ? $trans->response['action'] : '';
                                echo !empty($trans->response['action_date']) ? '<span data-toggle="tooltip" data-placement="bottom" title="' . $actionM . '">' . date('d,M y H:i', $trans->response['action_date']) . '</span>' : '' ?></td>
                            <td>{!! $clame !!}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
                {{ $transactions->appends($_GET)->links()}}
            </div>
            <!-- /.card-body -->

        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->

@include('retailer.transaction.dmt')
@include('retailer.transaction.payout')
@include('retailer.transaction.clame')
@endsection