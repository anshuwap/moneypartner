@extends('retailer.layouts.app')

@section('content')
@section('page_heading', 'Retailer List')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">

            <div class="card-header">
                <h3 class="card-title">Transaction List</h3>
                <div class="card-tools">

                    @if(!empty(MoneyPartnerOption()->recharge) && MoneyPartnerOption()->recharge ==1)
                    <a href="javascript:void(0);" id="mobileRecharge" class="btn btn-sm btn-success"><i class="fas fa-cloud-upload-alt"></i>&nbsp;Mobile Recharge</a>
                    @endif

                    <!-- <a href="{{ url('retailer/transaction-export') }}{{ !empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''}}" class="btn btn-sm btn-success"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export</a> -->
                    @if(!empty($filter))
                    <!-- <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a> -->
                    @else
                    <!-- <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a> -->
                    @endif

                </div>
            </div>


            <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
                <div class="col-md-12 ml-auto">
                    <form action="{{ url('retailer/transaction') }}">
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
                                    <option value="dmt_transfer" <?= (!empty($filter['type']) && $filter['type'] == 'success') ? 'selected' : '' ?>>DMT Transfer</option>
                                    <option value="payout" <?= (!empty($filter['type']) && $filter['type'] == 'payout') ? 'selected' : '' ?>>Payput</option>
                                    <option value="payout_api" <?= (!empty($filter['type']) && $filter['type'] == 'payout_api') ? 'selected' : '' ?>>Payout Api</option>
                                    <option value="bulk_payout" <?= (!empty($filter['type']) && $filter['type'] == 'bulk_payout') ? 'selected' : '' ?>>Bulk Payout</option>
                                </select>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                <a href="{{ url('retailer/transaction') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
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
                            <th>Transaction Id</th>
                            <th>Recharge Amount</th>
                            <th>Commission Amount</th>
                            <th>Paid Amount</th>
                            <th>Operator</th>
                            <th>Mobile/DTH No</th>
                            <th>Status</th>
                            <th>MSG</th>
                            <th>Request Date</th>
                            <!-- <th>Action</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recharges as $key=>$val)
                        <tr>
                            <td>{{++$key}}</td>
                            <td>{{$val->txn_id}}</td>
                            <td>{{$val->amount}}</td>
                            <td>{{$val->commission_fees}}</td>
                            <td>{{$val->paid_amount}}</td>
                            <td>{{$val->operator}}</td>
                            <td>{{$val->mobile_no}}</td>
                            <td>{{ucwords($val->staus)}}</td>
                            <td>{{$val->msg}}</td>
                            <td>{{date('d-m-y H:i A',$val->created)}}</td>
                            <!-- <th>Action</th> -->
                        </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>
            <!-- /.card-body -->

        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->

@include('retailer.services.mobile_recharge')
@endsection