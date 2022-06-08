@extends('retailer.layouts.app')
@section('content')


<div class="cover-loader d-none">
    <div class="loader"></div>
</div>

<div class="row">

    <div class="col-md-12 mt-2">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manual Credit Report</h3>

                <div class="card-tools">
                    <!-- <div class="card-body table-responsive py-2 table-sm">
                        <div class="float-right"> -->
                            <a href="{{ url('retailer/credit-export') }}{{ !empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''}}" class="btn btn-sm btn-success mr-2"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export</a>
                            @if(!empty($filter))
                            <a href="javascript:void(0);" class="btn btn-sm btn-success" id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
                            @else
                            <a href="javascript:void(0);" class="btn btn-sm btn-success" id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
                            @endif
                        </div>
                    </div>


                <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
                    <div class="col-md-12 ml-auto">
                        <form action="{{ url('retailer/credit-report') }}">
                            <div class="form-row">

                                <div class="form-group col-md-3">
                                    <label>Start Data</label>
                                    <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" name="start_date" />
                                </div>

                                <div class="form-group col-md-3">
                                    <label>End Data</label>
                                    <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" name="end_date" id="end-date" />
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Channel</label>
                                    <select name="payment_channel" class="form-control form-control-sm" id="payment_channel">
                                        <option value="">All</option>
                                        <?php foreach ($payment_channel as $channel) { ?>
                                            <option <?= (!empty($filter['payment_channel']) && $filter['payment_channel'] == $channel->name) ? "selected" : "" ?> value="<?= $channel->name ?>"><?= $channel->name ?></option>;
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                    <a href="{{ url('retailer/credit-report') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <div class="card-body table-responsive py-1">
                    <table id="table" class="table table-hover text-nowrap table-sm">
                        <thead>
                            <tr>
                                <th>Transaction Id</th>
                                <th>Channel</th>
                                <th>Amount</th>
                                <th>UTR No</th>
                                <th>Paid Status</th>
                                <th>Rquested Date</th>
                                <th>Created By</th>
                                <th>Modified Date</th>
                                <th>Mobified By</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($credits as $credit)
                            <tr>
                                <td>{{ $credit->transaction_id }}</td>
                                <td>{{ $credit->channel}}</td>
                                <td>{!!mSign($credit->amount)!!}</td>
                                <td>{{$credit->utr_no}}</td>
                                <!-- <td>{{ $credit->status }}</td> -->
                                <td>
                                    {{ ($credit->paid_status=='approved')?'Approved':'Due' }}
                                </td>
                                <td>{{ date('d M Y H:i',$credit->created)}}</td>
                                <td>{{ !empty($credit->UserName['full_name'])?$credit->UserName['full_name']:''}}</td>
                                <td><?php $actionM = !(empty($credit->action)) ? $credit->action : '';
                                    echo !empty($credit->action_date) ? '<span data-toggle="tooltip" data-placement="bottom" title="' . $actionM . '">' . date('d,M y H:i', $credit->action_date) . '</span>' : '' ?></td>
                                <td>{{ !empty($credit->ModifiedBy['full_name'])?$credit->ModifiedBy['full_name']:'-'}}</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $credits->appends(request()->toArray())->links() }}
                </div>
            </div>
        </div>
    </div>

    @endsection