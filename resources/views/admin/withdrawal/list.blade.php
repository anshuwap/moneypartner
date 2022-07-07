@extends('admin.layouts.app')
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
                    <a href="{{ url('employee/withdrawal-export') }}{{ !empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''}}" class="btn btn-sm btn-success mr-2"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export</a>
                </div>
            </div>


            <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
                <div class="col-md-12 ml-auto">
                    <form action="{{ url('admin/withdrawal') }}">
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
                                <label>Employee</label>
                                <select class="form-control-sm form-control" name="employee_id">
                                    <option value="" {{ (!empty($filter['employee_id']) && $filter['employee_id'] == 'all')?"selected":""}}>All</option>
                                    @foreach($employees as $employee)
                                    <option value="{{$employee->_id}}" {{ (!empty($filter['employee_id']) && $filter['employee_id'] == $employee->_id)?"selected":""}}>{{ ucwords($employee->full_name)}} </option>
                                    @endforeach
                                </select>
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
                                <a href="{{ url('admin/withdrawal') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
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
                            <th>Employee Name</th>
                            <th>Transaction Id</th>
                            <th>Amount</th>
                            <th>Acc. Holder</th>
                            <th>Acc. No.</th>
                            <th>IFSC Code</th>
                            <th>Status</th>
                            <th>Request Date</th>
                            <th>UTR No</th>
                            <th>Action By</th>
                            <th>Action Date</th>
                            <th>Action</th>
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
                        <td>{{ !empty($wt->EmployeeName['full_name'])?$wt->EmployeeName['full_name']:'-'}}</td>
                        <td>{{ $wt->transaction_id }}</td>
                        <td>{!! mSign($wt->amount) !!}</td>
                        <td>{{ $wt->account_holder }}</td>
                        <td>{{ $wt->account_number }}</td>
                        <td>{{ $wt->ifsc_code }}</td>
                        <td>{!! $status !!}</td>
                        <td>{{ date('d M Y H:i',$wt->created)}}</td>
                        <td>{{ $wt->utr_no }}</td>
                        <td>{{ !empty($wt->UserName['full_name'])?$wt->UserName['full_name']:'-'}}</td>
                        <td><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="{{ $wt->admin_comment}}">{{ !empty($wt->action_date)?date('d M Y H:i',$wt->action_date):'-'}}</a></td>
                        <td>
                            @if($wt->status=='pending')
                            <a href="javascript:void(0);" id_val="{{ $wt->_id}}" class="btn btn-success approve btn-xs"><i class="fas fa-hand-holding-usd nav-icon"></i>&nbsp;Approve</a>
                            @else
                            -
                            @endif
                        </td>

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

<div class="modal fade" id="approve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_approve">Request For Withdrawal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body">
                <form id="approve" action="{{ url('admin/withdrawalW') }}" method="post">
                    @csrf
                    <div id="put"></div>
                    <div class="row">
                        <input type="hidden" id="_id" name="id" value="0">
                        <div class="col-md-12">

                            <!-- <div class="form-group">
                                <label>Amount</label>
                                <input type="number" placeholder="Enter Amount" id="amount" required name="amount" class="form-control form-control-sm">
                                <span id="amount_msg" class="custom-text-danger"></span>
                            </div> -->

                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control form-control-sm" name="status" id="status">
                                    <option value="">Select</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>

                            <div id="utr"></div>

                            <div class="form-group">
                                <label>Comment (Optional)</label>
                                <textarea class="form-control" name="comment" id="comment" rows="3"></textarea>
                                <span id="comment_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-success btn-sm" id="submit_modal" value="Submit">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $('.approve').click(function(e) {
        e.preventDefault();
        $('#_id').val($(this).attr('id_val'));
        $('form#approve')[0].reset();
        let url = '{{ url("admin/withdrawal") }}';
        $('#heading_topup').html('Approve Withdrawal');
        $('#put').html('');
        $('form#approve').attr('action', url);
        $('#submit_approve').val('Submit');
        $('#approve_modal').modal('show');

    })

    $('#status').change(function() {
        let status = $(this).val();
        if (status == 'approved') {
            $('#utr').html(`<div class="form-group">
                                <label>UTR No</label>
                                <input type="number" placeholder="Enter UTR No" id="amount" required name="utr_no" class="form-control form-control-sm">
                                <span id="amount_msg" class="custom-text-danger"></span>
                            </div>`);
        } else if (status == 'rejected') {
            $('#utr').html('');
        }
    })

    /*start form submit functionality*/
    $("form#approve").submit(function(e) {
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