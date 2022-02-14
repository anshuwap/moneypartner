@extends('employee.layouts.app')

@section('content')
@section('page_heading', 'admin List')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">

            <div class="covertabs-btn __web-inspector-hide-shortcut__">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a href="{{ url('employee/e-customer-trans') }}" class="nav-link ">DMT Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('employee/e-retailer-trans') }}" class="nav-link active">Payout Transaction</a>
                    </li>
                </ul>
                <div class="add-btn w-50">
                    <form action="{{ url('employee/e-retailer-trans') }}" method="GET">
                        <div class="form-row mr-4 mt-1">

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm float-right" name="date_range" id="daterange-btn">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <select class="form-control-sm form-control" name="outlet_id">
                                    <option>Select</option>
                                    @foreach($outlets as $outlet)
                                    <option value="{{$outlet->_id}}" {{ ($outlet_id ==$outlet->_id)?"selected":""}}>{{ ucwords($outlet->outlet_name)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i> &nbsp;serach</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- /.card-header -->
            <div class="card-body table-responsive py-4 table-sm">
                <table id="" class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Sender Name</th>
                            <th>Mobile No.</th>
                            <th>Amount</th>
                            <th>Receiver Name</th>
                            <th>Payment Mode</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($retailerTrans as $key=>$trans)
                        <?php if ($trans->status == 'approved') {
                            $status = '<strong class="text-success">' . ucwords($trans->status) . '</strong>';
                            $action = '-';
                        } else if ($trans->status == 'rejected') {
                            $status = '<strong class="text-danger">' . ucwords($trans->status) . '</strong>';
                            $action = '-';
                        } else {

                            $status = '<strong class="text-warning">' . ucwords($trans->status) . '</strong>';
                            $action = '<a href="javascript:void(0);" class="btn btn-danger btn-sm retailer_trans" _id="'.$trans->_id.'">Action</a>';
                        } ?>
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ ucwords($trans->sender_name) }}</td>
                            <td>{{ $trans->mobile_number }}</td>
                            <td>{!! mSign($trans->amount) !!}</td>
                            <td>{{ ucwords($trans->receiver_name)}}</td>
                            <td>{{ $trans->payment_mode }}</td>
                            <td>{!! $status !!}</td>
                            <td>{{ date('Y-m-d',$trans->created) }}</td>
                            <td> <a href="javascript:void(0);" class="btn btn-info btn-sm view" _id="{{ $trans->_id }}"><i class="fas fa-eye"></i>&nbsp;view</a>
                                {!! $action !!}</td>
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

@push('modal')

<!-- Modal -->
<div class="modal fade" id="approve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_bank">Approved/Reject Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body">
                <form id="approve_trans" action="{{ url('employee/e-retailer-trans') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="trans_id" name="trans_id">
                            <input type="hidden" id="key" name="key">

                            <div class="form-group">
                                <label>Action</label>
                                <select name="status" id="status-select" class="form-control form-control-sm">
                                    <option value="">Select</option>
                                    <option value="approved">Approved</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                <span id="status_msg" class="custom-text-danger"></span>
                            </div>

                            <div id="approved"></div>

                            <div class="form-group" id="comment-field" style="display: none;">
                                <label>Comment</label>
                                <select name="comment" class="form-control form-control-sm" id="comment">

                                </select>
                                <span id="comment_msg" class="custom-text-danger"></span>
                            </div>

                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-success btn-sm" value="Submit">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_bank">Account Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body" id="details1">
                <div id="details"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.retailer_trans', function(e) {
        e.preventDefault();
        $('#trans_id').val($(this).attr('_id'));
        $('#approve_modal').modal('show');
    })


     //show transaction detils
    $(document).on('click', '.view', function() {
        var _id = $(this).attr('_id');
        $.ajax({
            url: "<?= url('employee/e-retailer-detail') ?>",
            data: {
                'id': _id,
            },
            type: 'GET',
            dataType: "json",
            success: function(res) {

                $('#details').html(res);
                $('#view_modal').modal('show');
            }
        })
    });


    $('#status-select').change(() => {
        let status = $('#status-select').val();
        if (status == 'approved') {
            $('#approved').html(`<div class="form-group">
                                <label>UTR/Transaction</label>
                                <input type="text" placeholder="UTR/Transaction" id="utr" name="admin_action['utr_transaction']" class="form-control form-control-sm">
                                <span id="utr_transaction_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label>Select Payment Channel</label>
                                <select name="admin_action['payment_mode']" class="form-control form-control-sm" id="payment_channel" >
                                    <option value="">Select</option>
                                    <?php foreach ($payment_channel as $channel) {
                                        echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                                    } ?>
                                </select>
                                <span id="payment_channel_msg" class="custom-text-danger"></span>
                            </div>`);
        } else {
            $('#approved').html(``);
        }
    })


    $('#status-select').change(function(e) {
        e.preventDefault();

        var type = $(this).val();

        if (type == '') {
            $('#comment-field').hide();
        } else {
            $.ajax({
                url: "<?= url('employee/e-retailer-comment') ?>",
                data: {
                    'type': type
                },
                type: 'GET',
                dataType: "json",
                success: function(res) {
                    $('#comment-field').show();
                    $('#comment').html(res);
                }
            })
        }
    })

    /*start form submit functionality*/
    $("form#approve_trans").submit(function(e) {
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
                    $('form#approve_trans')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 1000)
                }
            }
        });
    });

    /*end form submit functionality*/

    function copyToClipboard(element, copy) {
        var $temp = $("<input />");
        $("#details1").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $(copy).removeClass('d-none');
        $temp.remove();
    }
</script>

@endpush
@endsection