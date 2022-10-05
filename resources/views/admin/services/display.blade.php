@extends('admin.layouts.app')

@section('content')
@section('page_heading', 'Recharge List')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">

            <div class="card-header">
                <h3 class="card-title">Transaction List</h3>
                <div class="card-tools">
                    <a href="{{ url('admin/service-export') }}{{ !empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''}}" class="btn btn-sm btn-success"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export</a>
                    @if(!empty($filter))
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
                    @else
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
                    @endif
                </div>
            </div>


            <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
                <div class="col-md-12 ml-auto">
                    <form action="{{ url('admin/services') }}">
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
                                <label>Mobile/DTH No</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Mobile/DTH No" value="<?= !empty($filter['mobile_no']) ? $filter['mobile_no'] : '' ?>" name="mobile_no" id="mobile_no" />
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
                                <label>Operator</label>
                                <select class="form-control form-control-sm" name="operator">
                                    <option value="">All</option>
                                    @foreach(rechargeOperator() as $key=>$operator)
                                    <option value="{{$key}}" <?= (!empty($filter['operator']) && $filter['operator'] == $key) ? 'selected' : '' ?>>{{$operator}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label>Status</label>
                                <select class="form-control form-control-sm" name="status">
                                    <option value="">All</option>
                                    <option value="success" <?= (!empty($filter['status']) && $filter['status'] == 'success') ? 'selected' : '' ?>>Success</option>
                                    <option value="pending" <?= (!empty($filter['status']) && $filter['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="failed" <?= (!empty($filter['status']) && $filter['status'] == 'failed') ? 'selected' : '' ?>>Failed</option>
                                </select>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                <a href="{{ url('admin/services') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
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
                            <th>Outlet Name</th>
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
                        <?php
                        if ($val->status == 'success') {
                            $status = '<span class="tag-small"><a href="javascript:void(0)" class="text-dark">' . ucwords($val->status) . '</a></span>';
                        } else if ($val->status == 'failed') {
                            $status = '<span class="tag-small-danger"><a href="javascript:void(0)" class="text-dark">' . ucwords($val->status) . '</a></span>';
                        } else {
                            $status = '<span class="tag-small-warning"><a href="javascript:void(0)" class="text-dark">Pending</a></span>';
                        } ?>


                        <tr>
                            <td>{{++$key}}</td>
                            <td>{{$val->txn_id}}</td>
                            <td>{{!empty($val->RetailerName['outlet_name']) ? $val->RetailerName['outlet_name'] : ''}}</td>
                            <td>{{$val->amount}}</td>
                            <td>{{$val->commission_fees}}</td>
                            <td>{{$val->paid_amount}}</td>
                            <td>{{checkOperator($val->operator)}}</td>
                            <td>{{$val->mobile_no}}</td>
                            <td>{!!$status!!}</td>
                            <td>{{$val->msg}}</td>
                            <td>{{date('d-m-y H:i A',$val->created)}}</td>
                            <!-- <th>Action</th> -->
                        </tr>
                        @endforeach
                    </tbody>

                </table>
 {{ $recharges->appends($_GET)->links()}}
            </div>
            <!-- /.card-body -->

        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->

@include('retailer.services.mobile_recharge')
@endsection