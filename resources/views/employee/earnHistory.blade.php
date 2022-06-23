@extends('employee.layouts.app')
@section('content')
@section('page_heading', 'Spent Amount Topup List')


<div class="row">

    <div class="col-12 mt-2">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">Earned History</h3>

                <div class="card-tools">
                    @if(!empty($filter))
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
                    @else
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
                    @endif

                </div>
            </div>


            <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
                <div class="col-md-12 ml-auto">
                    <form action="{{ url('employee/earn-history') }}">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label>Start Data</label>
                                <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" name="start_date" />
                            </div>

                            <div class="form-group col-md-2">
                                <label>End Data</label>
                                <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" name="end_date" id="end-date" />
                            </div>

                            <!-- <div class="form-group col-md-2">
                                <label>Transaction ID</label>
                                <input type="text"  placeholder="Transaction ID" class="form-control form-control-sm" value="<?= !empty($filter['transaction_id']) ? $filter['transaction_id'] : '' ?>" name="transaction_id" id="transaction_id" />
                            </div> -->

                            <div class="form-group col-md-2">
                                <label>Outlet Name</label>
                                <select class="form-control-sm form-control" name="outlet_id">
                                    <option value="" {{ (!empty($filter['outlet_id']) && $filter['outlet_id'] == 'all')?"selected":""}}>All</option>
                                    @foreach($outlets as $outlet)
                                    <option value="{{$outlet->_id}}" {{ (!empty($filter['outlet_id']) && $filter['outlet_id'] == $outlet->_id)?"selected":""}}>{{ ucwords($outlet->outlet_name)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                <a href="{{ url('employee/earn-history') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- /.card-header -->
            <div class="card-body table-responsive py-1">

                <table id="table" class="table table-hover text-nowrap table-sm">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Outlet Name</th>
                            <th>Transaction Time</th>
                            <th>Transaction No</th>
                            <th>Action By</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Closing Amount</th>
                        </tr>
                    </thead>

                    @foreach($earnHistory as $key=>$earn)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ !empty($earn->OutletName['outlet_name'])?$earn->OutletName['outlet_name']:'-'}}</td>
                        <td>{{ date('Y-m-d H:i:s',$earn->created)}}</td>
                        <td>{{ !empty($earn->Transaction['transaction_id'])?$earn->Transaction['transaction_id']:'-'}}</td>
                        <td>{{ !empty($earn->ActionBy['full_name'])?$earn->ActionBy['full_name']:'-'}}</td>
                        <td>{!!mSign($earn->amount)!!}</td>
                        <td><span class="text-success">{{ !empty($earn->type)?strtoupper($earn->type):'-' }}</span></td>
                        <td>{!! mSign($earn->closing_amount) !!}</td>
                    </tr>
                    @endforeach

                </table>

                {{ $earnHistory->appends($_GET)->links()}}
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->


@endsection