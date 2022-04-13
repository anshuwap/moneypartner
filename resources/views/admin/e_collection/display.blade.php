@extends('admin.layouts.app')

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
                    <form action="{{ url('admin/e-collection') }}">
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
                                <label>Outlet</label>
                                <select class="form-control form-control-sm" name="outlet_id">
                                    <option value="">All</option>
                                    @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->_id}}" <?= (!empty($filter['outlet_id']) && $filter['outlet_id'] == $outlet->_id) ? 'selected' : '' ?>>{{ ucwords($outlet->outlet_name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                <a href="{{ url('admin/e-collection') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
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
                            <th>Action</th>
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
                            <td>{!! mSign($ecoll->amount)!!}</td>
                            <td>{{ ucwords($ecoll->status) }}</td>
                            <td>
                                @if($ecoll->wallet_status =='approved')
                                <span class="tag-small"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="{{$ecoll->remark}}">{{ucwords($ecoll->wallet_status)}}</a></span>
                                @else
                                <span class="tag-small-warning"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="{{$ecoll->remark }}">Pending</a></span></th>
                                @endif
                            </td>
                            <td>
                                @if($ecoll->wallet_status != 'approved' && $ecoll->status == 'SUCCESS')
                                <a href="javascript:void(0);" class="btn btn-success btn-xs credit" _id="{{ $ecoll->_id }}"><i class="fab fa-codepen"></i>&nbsp;Settlement</a>
                                @endif
                            </td>

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
@push('custom-script')
<script>
    $('.credit').click(function() {
        var id = $(this).attr('_id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2fc296',
            cancelButtonColor: '#e26005 ',
            confirmButtonText: 'Yes, Approved'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: '{{ url("admin/e-collection") }}',
                    type: 'POST',
                    data: {
                        'id': id,
                        'status': status
                    },
                    dataType: "json",
                    success: function(res) {
                        /*Start Status message*/
                        if (res.status == 'error' || res.status == 'success') {
                            Swal.fire(
                                `${res.status}!`,
                                res.msg,
                                `${res.status}`,
                            )
                        }
                        /*End Status message*/
                        if (res.status == 'success') {
                            setTimeout(function() {
                                location.reload();
                            }, 1000)
                        }
                    }
                });
            }
        })

    });
</script>
@endpush

@endsection