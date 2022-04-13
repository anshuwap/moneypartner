@extends('distributor.layouts.app')
@section('content')
@section('page_heading', 'Spent Amount Topup List')

<div class="row">

    <div class="col-12 mt-2">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">Passbook</h3>

                <div class="card-tools">
                    <!-- <a href="javascript:void(0);" class="btn btn-sm btn-success mr-2" id="create_topup"><i class="fas fa-hand-holding-usd"></i>&nbsp;Request for Topup</a> -->

                    @if(!empty($filter))
                    <a href="javascript:void(0);" class="btn btn-sm btn-success mr-2" id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
                    @else
                    <a href="javascript:void(0);" class="btn btn-sm btn-success mr-2" id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
                    @endif
                    <a href="{{ url('distributor/passbook-export') }}{{ !empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''}}" class="btn btn-sm btn-success mr-2"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export</a>
                </div>
            </div>


            <div class="row pl-2 pr-2" id="filter" <?=(empty($filter))?"style='display:none'":""?>>
                <div class="col-md-12 ml-auto">
                    <form action="{{ url('distributor/passbook') }}">
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
                                <label>Type</label>
                                <select class="form-control form-control-sm" name="type">
                                    <option value="">All</option>
                                    <option value="credit" <?= (!empty($filter['type']) && $filter['type']=='credit')?'selected':'' ?>>Credit</option>
                                    <option value="debit" <?= (!empty($filter['type']) && $filter['type']=='debit')?'selected':'' ?>>Debit</option>
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label>Outlet</label>
                                <select class="form-control form-control-sm" name="outlet_id">
                                    <option value="">All</option>
                                    @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->_id}}" <?= (!empty($filter['outlet_id']) && $filter['outlet_id']==$outlet->_id)?'selected':'' ?>>{{ ucwords($outlet->outlet_name) }}</option>
                                   @endforeach
                                </select>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                <a href="{{ url('distributor/passbook') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
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
                            <th>Mode</th>
                            <th>Transaction Amount</th>
                            <th>Fees</th>
                            <th>Closing Amount</th>
                            <th>Credit/Debit</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    @foreach($passbook as $key=>$pb)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ (!empty($pb->OutletName['outlet_name']))?$pb->OutletName['outlet_name']:'-'}}</td>
                        <td>{{ date('Y-m-d H:i:s',$pb->created)}}</td>
                        <td>{{ !empty($pb->transaction_type)?ucwords(str_replace('_',' ',$pb->transaction_type)):'-' }}</td>
                        <td>{!!mSign($pb->amount)!!}</td>
                        <td>{!!(!empty($pb->fees))?mSign($pb->fees):'-' !!}</td>
                        <td>{!!mSign($pb->closing_amount)!!}</td>

                        @if($pb->type == 'credit')
                        <td class="text-success">{{ ucfirst($pb->type) }}</td>
                        @elseif($pb->type == 'debit')
                        <td class="text-danger">{{ ucfirst($pb->type) }}</td>
                        @else
                         <td class="text-danger">-</td>
                        @endif
                        <td>{{ ucfirst($pb->status) }}</td>
                    </tr>
                    @endforeach

                </table>

                {{ $passbook->appends($_GET)->links()}}
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->

@push('modal')
<!-- Modal -->


<script>

    $('#payment_reference').change(function() {
        var payment_mode = $('#payment_mode').val();
        var payment_id = $(this).val();
        $.ajax({
            url: "{{ url('retailer/payment-details') }}",
            data: {
                'payment_mode': payment_mode,
                'payment_id': payment_id
            },
            type: 'GET',
            dataType: 'JSON',
            success: function(res) {
                console.log(res);
                $('#show-paymnet-details').html(res);
            }
        })
    });

</script>


@endpush

@endsection