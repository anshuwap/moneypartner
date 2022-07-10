@extends('employee.layouts.app')
@section('content')
@section('page_heading', 'Spent Amount Topup List')

<div class="row">

    <div class="col-12 mt-2">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">Withdrawal List</h3>

                <div class="card-tools">
                    <a href="javascript:void(0);" class="btn btn-sm btn-success mr-1" id="withdrawal"><i class="fas fa-hand-holding-usd"></i>&nbsp;Withdrawal</a>

                    @if(!empty($filter))
                    <a href="javascript:void(0);" class="btn btn-sm btn-success mr-1" id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
                    @else
                    <a href="javascript:void(0);" class="btn btn-sm btn-success mr-1" id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
                    @endif
                    <!-- <a href="{{ url('employee/passbook-export') }}{{ !empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''}}" class="btn btn-sm btn-success mr-2"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export</a> -->
                </div>
            </div>


            <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
                <div class="col-md-12 ml-auto">
                    <form action="{{ url('employee/withdrawal') }}">
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
                                <label>Status</label>
                                <select class="form-control form-control-sm" name="status">
                                    <option value="">All</option>
                                    <option value="approved" <?= (!empty($filter['status']) && $filter['status'] == 'approved') ? 'selected' : '' ?>>Approved</option>
                                    <option value="rejected" <?= (!empty($filter['status']) && $filter['status'] == 'rejected') ? 'selected' : '' ?>>Rejected</option>
                                    <option value="pending" <?= (!empty($filter['status']) && $filter['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label>Transaction Id</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Transaction ID" value="<?= !empty($filter['transaction_id']) ? $filter['transaction_id'] : '' ?>" name="transaction_id" id="transaction_id" />
                            </div>

                            <div class="form-group col-md-2">
                                <label>UTR NO</label>
                                <input type="text" class="form-control form-control-sm" placeholder="UTR NO" value="<?= !empty($filter['utr_no']) ? $filter['utr_no'] : '' ?>" name="utr_no" id="utr_no" />
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                <a href="{{ url('employee/withdrawal') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
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
                            <th>Transaction Id</th>
                            <th>Amount</th>
                            <th>Acc. Holder</th>
                            <th>Acc. No.</th>
                            <th>IFSC Code</th>
                            <th>Status</th>
                            <th>Request Date</th>
                            <th>Action By</th>
                            <th>UTR No</th>

                            <th>Action Date</th>
                        </tr>
                    </thead>
                    @foreach($withdrawals as $key=>$wt)
                    <?php
                    $comment = !empty($wt->comment) ? $wt->comment : '';
                    $status = '';
                    if ($wt->status == 'approved') {
                        $status = '<span class="tag-small"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($wt->status) . '</a></span>';
                    } else if ($wt->status == 'pending') {
                        $status = '<span class="tag-small-warning"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($wt->status) . '</a></span>';
                    } else if ($wt->status == 'rejected') {
                        $status = '<span class="tag-small-danger"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($wt->status) . '</a></span>';
                    }
                    ?>
                    <tr>
                        <td>{{ ++$key}}</td>
                        <td>{{ $wt->transaction_id }}</td>
                        <td>{!! mSign($wt->amount) !!}</td>
                        <td>{{ $wt->account_holder }}</td>
                        <td>{{ $wt->account_number }}</td>
                        <td>{{ $wt->ifsc_code }}</td>
                        <td>{!! $status !!}</td>
                        <td>{{ date('d M Y H:i',$wt->created)}}</td>
                        <td>{{ !empty($wt->UserName['full_name'])?$wt->UserName['full_name']:'-'}}</td>
                        <td>{{ $wt->utr_no }}</td>

                        <td><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="{{ $wt->admin_comment}}">{{ !empty($wt->action_date)?date('d M Y H:i',$wt->action_date):'-'}}</a></td>
                    </tr>
                    @endforeach
                </table>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->

@push('modal')
<!-- Modal -->

<div class="modal fade" id="withdrawal_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_totup">Request For Withdrawal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body">
                <form id="withdrawal" action="{{ url('employee/withdrawal') }}" method="post">
                    @csrf
                    <div id="put"></div>
                    <div class="row">
                        <div class="border-bottom"><b>Total Earned Amount :-&nbsp;&nbsp;</b><span class="float-right">{!! mSign(Auth::user()->wallet_amount) !!}</span></div>
                        <div class="col-md-12">

                            <div class="form-group">
                                <label>Amount</label>
                                <input type="number" placeholder="Enter Amount" id="amount" required name="amount" class="form-control form-control-sm">
                                <span id="amount_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label>Acc. Holder Name</label>
                                <input type="text" placeholder="Enter Acc. Holder Name" id="account_holder" required name="account_holder" class="form-control form-control-sm">
                                <span id="account_holder_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label>Acc. Number</label>
                                <input type="number" placeholder="Enter Acc. Number" id="account_no" required name="account_number" class="form-control form-control-sm">
                                <span id="account_no_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label>IFSC Code</label>
                                <input type="text" placeholder="Enter IFSC Code" id="ifsc_code" required name="ifsc_code" class="form-control form-control-sm">
                                <span id="ifsc_code_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label>Comment (Optional)</label>
                                <textarea class="form-control" name="comment" id="comment" rows="3"></textarea>
                                <span id="comment_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-success btn-sm" id="submit_withdrawal" value="Submit">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $('#withdrawal').click(function(e) {
        e.preventDefault();

        $('form#withdrawal')[0].reset();
        let url = '{{ url("employee/withdrawal") }}';
        $('#heading_topup').html('Request For Topup');
        $('#put').html('');
        $('form#withdrawal').attr('action', url);
        $('#submit_withdrawal').val('Submit');
        $('#withdrawal_modal').modal('show');

    })

    /*start form submit functionality*/
    $("form#withdrawal").submit(function(e) {
        e.preventDefault();
        formData = new FormData(this);
        var url = $(this).attr('action');
        $.ajax({
            data: formData,
            type: "POST",
            url: url,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('.cover-loader-modal').removeClass('d-none');
                $('.modal-body').hide();
            },
            success: function(res) {
                //hide loader
                $('.cover-loader-modal').addClass('d-none');
                $('.modal-body').show();

                /*Start Validation Error Message*/
                $('span.custom-text-danger').html('');
                $.each(res.validation, (index, msg) => {
                    $(`#${index}_msg`).html(`${msg}`);
                })
                /*Start Validation Error Message*/

                /*Start Status message*/
                if (res.status == 'success' || res.status == 'error') {
                    Swal.fire(
                        `${res.status}!`,
                        res.msg,
                        `${res.status}`,
                    )
                }
                /*End Status message*/

                //for reset all field
                if (res.status == 'success') {
                    $('form#withdrawal')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 1000)
                }
            }
        });
    });
    /*end form submit functionality*/
</script>

@endpush

@endsection