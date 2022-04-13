@extends('retailer.layouts.app')

@section('content')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">E-collection</h3>
                <div class="card-tools">
                    @if(!empty($filter))
                    <a href="javascript:void(0);" class="btn btn-sm btn-success mr-2" id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
                    @else
                    <a href="javascript:void(0);" class="btn btn-sm btn-success mr-2" id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
                    @endif
                </div>
            </div>


            <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
                <div class="col-md-12 ml-auto">
                    <form action="{{ url('retailer/e-collection') }}">
                        <div class="form-row">

                            <div class="form-group col-md-2">
                                <label>Start Data</label>
                                <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" name="start_date" />
                            </div>

                            <div class="form-group col-md-2">
                                <label>End Data</label>
                                <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" name="end_date" id="end-date" />
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                <a href="{{ url('retailer/e-collection') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive ">
                <table id="table" class="table table-hover table-sm text-nowrap">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Datetime</th>
                            <th>Transaction Id</th>
                            <th>Outlet Name</th>
                            <th>Payer Name</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Settlement Status</th>
                            <!-- <th>Created Date</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ecollections as $key=>$ecoll)
                        <tr>
                            <td>{{ ++$key}}</td>
                            <td>{{ date('d M Y H:i',$ecoll->date) }}</td>
                            <td>{{ $ecoll->transaction_id}}</td>
                            <td>{{ ucwords($ecoll->outlet_name)}}</td>
                            <td>{{ ucwords($ecoll->payer_name) }}</td>
                            <td>{{ $ecoll->amount}}</td>
                            <td>{{ ucwords($ecoll->status) }}</td>
                             <td>
                                @if($ecoll->wallet_status =='approved')
                                <span class="tag-small"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="{{$ecoll->remark}}">{{ucwords($ecoll->wallet_status)}}</a></span>
                                @else
                                @if($ecoll->status != 'pending')
                                <span class="tag-small-warning"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="{{$ecoll->remark }}">Pending</a></span></th>
                                @endif
                                @endif
                            </td>
                            <!-- <td>{{ date('d,M Y',$ecoll->created)}}</td> -->

                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $ecollections->appends(request()->toArray())->links() }}
            </div>
            <!-- /.card-body -->

        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->


@endsection