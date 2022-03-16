@extends('employee.layouts.app')
@section('content')
<style>

</style>
<div class="row">
    <div class="col-12 mt-2">
        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col-md-10">
                        <h3 class="card-title">Transaction List</h3>
                    </div>
                    <div class="col-md-2">

                        @if(!empty($filter))
                        <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
                        @else
                        <a href="javascript:void(0);" class="btn btn-sm btn-success " id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
                        @endif
                        <a href="{{ url('employee/a-transaction-export') }}{{ !empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''}}" class="btn btn-sm btn-success mr-2"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export</a>

                    </div>
                </div>
            </div>

            <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
                <div class="col-md-12 ml-auto">
                    <form action="{{ url('employee/a-transaction') }}">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label>Data Range</label>
                                <input type="text" class="form-control form-control-sm" value="<?= !empty($filter['date_range']) ? $filter['date_range'] : '' ?>" name="date_range" id="daterange-btn" />
                            </div>

                            <div class="form-group col-md-2">
                                <label>Transaction Id</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Transaction ID" value="<?= !empty($filter['transaction_id']) ? $filter['transaction_id'] : '' ?>" name="transaction_id" id="transaction_id" />
                            </div>

                            <div class="form-group col-md-2">
                                <label>Outlet Name</label>
                                <select class="form-control-sm form-control" name="outlet_id">
                                    @foreach($outlets as $outlet)
                                    <option value="{{$outlet->_id}}" {{ (!empty($filter['outlet_id']) && $filter['outlet_id'] == $outlet->_id)?"selected":""}}>{{ ucwords($outlet->outlet_name)}}</option>
                                    @endforeach
                                    <option value="all" {{ (!empty($filter['outlet_id']) && $filter['outlet_id'] == 'all')?"selected":""}}>All</option>
                                </select>
                            </div>

                            <!-- <div class="form-group col-md-2">
                                <label>Mode</label>
                                <select class="form-control form-control-sm" name="mode">
                                    <option value="">All</option>
                                    <option value="bank_account" <?= (!empty($filter['mode']) && $filter['mode'] == 'bank_account') ? 'selected' : '' ?>>Bank Account</option>
                                    <option value="upi" <?= (!empty($filter['mode']) && $filter['mode'] == 'upi') ? 'selected' : '' ?>>UPI</option>
                                </select>
                            </div> -->

                            <div class="form-group col-md-2">
                                <label>Type</label>
                                <select class="form-control form-control-sm" name="type">
                                    <option value="">All</option>dmt_transfer
                                    <option value="dmt_transfer" <?= (!empty($filter['type']) && $filter['type'] == 'success') ? 'selected' : '' ?>>DMT Transfer</option>
                                    <option value="payout" <?= (!empty($filter['type']) && $filter['type'] == 'payout') ? 'selected' : '' ?>>Payput</option>
                                    <option value="payout_api" <?= (!empty($filter['type']) && $filter['type'] == 'payout_api') ? 'selected' : '' ?>>Payout Api</option>
                                     <option value="bulk_payout" <?= (!empty($filter['type']) && $filter['type'] == 'bulk_payout') ? 'selected' : '' ?>>Bulk Payout</option>
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label>Status</label>
                                <select class="form-control form-control-sm" name="status">
                                    <option value="">All</option>
                                    <option value="success" <?= (!empty($filter['status']) && $filter['status'] == 'success') ? 'selected' : '' ?>>Success</option>
                                    <option value="pending" <?= (!empty($filter['status']) && $filter['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="reject" <?= (!empty($filter['status']) && $filter['status'] == 'reject') ? 'selected' : '' ?>>Reject</option>
                                </select>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                <a href="{{ url('employee/a-transaction') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- /.card-header -->
            <div class="card-body table-responsive py-2 table-sm">
                <table id="table" class="table table-hover text-nowrap table-sm">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Outlet</th>
                            <th>Transaction Id</th>
                            <th>Mode</th>
                            <th>Amount</th>
                            <th>Fees</th>
                            <th>Beneficiary</th>
                            <th>IFSC</th>
                            <th>Account No.</th>
                            <th>Bank Name</th>
                            <th>UTR No.</th>
                            <th>Status</th>
                            <th>Datetime</th>

                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction as $key=>$trans)
                        <?php

                        $payment = (object)$trans->payment_channel;
$comment = !empty($trans->response['msg']) ? $trans->response['msg'] : '';
                        if ($trans->status == 'success') {
                            $status = '<span class="tag-small"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="'.$comment.'">' . ucwords($trans->status) . '</a></span>';
                            $action = '';
                        } else if ($trans->status == 'rejected') {
                            $status = '<span class="tag-small-danger"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="'.$comment.'">' . ucwords($trans->status) . '</a></span>';
                            $action = '';
                        } else {

                            $status = '<span class="tag-small-warning"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="'.$comment.'">' . ucwords($trans->status) . '</a></span>';
                            $action = '<a href="javascript:void(0);" payment_mode="' . $trans->payment_mode . '" class="btn btn-danger btn-sm retailer_trans" _id="' . $trans->_id . '">Action</a>';
                        } ?>
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td><div style="display: grid;"><span>{{ ucwords($trans->sender_name)}}</span><span style="font-size: 13px;">{{ $trans->mobile_number }}</span></div></td>
                            <td>{{ $trans->transaction_id }}
                            </td>
                            <td><span class="tag-small">{{ ucwords(str_replace('_',' ',$trans->type)) }}</span></td>
                            <td>{!! mSign($trans->amount) !!}</td>
                            <td>{!! mSign($trans->transaction_fees) !!}</td>
                            <td>{{ ucwords($trans->receiver_name)}}</td>
                            <td>{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</td>
                            <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
                                <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
                            </td>
                            <td><?= (!empty($payment->bank_name)) ? $payment->bank_name : '-' ?></td>
                              <td> <?= (!empty($trans->response['utr_number'])) ? $trans->response['utr_number']: '-' ?></td>
                            <td>{!! $status !!}</td>
                            <td>{{ date('d,M y H:i A',$trans->created) }}</td>

                            <td> <a href="javascript:void(0);" class="btn btn-info btn-sm view_dashboard" _id="{{ $trans->_id }}"><i class="fas fa-eye"></i>&nbsp;view</a>
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

<!--start retailer transfer module-->
@push('modal')

<!-- Modal -->
<div class="modal fade" id="approve_modal_dashboard" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_bank_dashboard">success/Reject Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body">
                <form id="approve_trans_dashboard" action="{{ url('employee/a-transaction') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="trans_id_dahboard" name="trans_id">
                            <input type="hidden" id="key_dashboard" name="key">

                            <div class="form-group" id="type-m">
                                <label>Select</label>
                                <select name="type" class="form-control form-control-sm" id="type">
                                    <option value="">Select</option>
                                    <option value="api">Api</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>

                            <div id="action">
                            </div>

                            <div id="success_dashboard"></div>

                            <div class="form-group" id="comment-field_dashboard" style="display: none;">
                                <label>Comment</label>
                                <select name="comment" class="form-control form-control-sm" id="comment_dashboard">

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
<div class="modal fade" id="view_modal_dashboard" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_bank_dashboard">Account Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body" id="details1_dashboard">
                <div id="details_dashboard"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.retailer_trans', function(e) {
        e.preventDefault();
        $('#trans_id_dahboard').val($(this).attr('_id'));
        $('#key_dashboard').val(key_dashboard);

        var payment_mode = $(this).attr('payment_mode');
        if (payment_mode == 'upi') {
            $('#type-m').hide();
            $('#success_dashboard').html(``);
            upi();
        } else {
            $('#type-m').show();
            $('#success_dashboard').html(``);
            $('#action').html(``);
        }
        $('#approve_modal_dashboard').modal('show');
    })


          function upi() {
          $('#action').html(` <div class="form-group">
                                   <label>Action</label>
                                   <select name="status" id="status-select-dashboard" class="form-control form-control-sm">
                                       <option value="">Select</option>
                                       <option value="success">Success</option>
                                       <option value="pending">Pending</option>
                                       <option value="rejected">Rejected</option>
                                   </select>
                                   <span id="status_msg" class="custom-text-danger"></span>
                               </div>

                                <div class="form-group" id="challel">
                                   <label>Select Payment Channel</label>
                                   <select name="response[payment_mode]" class="form-control form-control-sm" id="payment_channel">
                                       <option value="">Select</option>
                                       <?php foreach ($payment_channel as $channel) {
                                            echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                                        } ?>
                                   </select>
                                   <span id="payment_channel_msg" class="custom-text-danger"></span>
                               </div>`);
      }


      $('#type').change(() => {
          let status = $('#type').val();
          if (status == 'manual') {
              $('#approve_trans_dashboard').attr('action', '{{url("employee/a-transaction")}}');
              $('#action').html(` <div class="form-group">
                                   <label>Action</label>
                                   <select name="status" id="status-select-dashboard" class="form-control form-control-sm">
                                       <option value="">Select</option>
                                       <option value="success">success</option>
                                       <option value="pending">Pending</option>
                                       <option value="rejected">Rejected</option>
                                   </select>
                                   <span id="status_msg" class="custom-text-danger"></span>
                               </div>`);
          } else if (status == 'api') {
              $('#approve_trans_dashboard').attr('action', '{{url("employee/a-store-api")}}');
              $('#action').html(`<div class="form-group">
               <select class="form-control form-control-sm" name="api" id="api" required>
               <option value=''>Select</option>
               <option value="payunie_preet_kumar">Payunie - PREET KUMAR</option>
               <option value="payunie_parveen">Payunie - PRAVEEN</option>
               <option value="pay2all">Pay2ALL - PRAVEEN</option>
               </select>
               </div>`);
          }
      })


      $(document).on('change', '#status-select-dashboard', function() {
          let status = $('#status-select-dashboard').val();

          if (status == 'success') {
            $('#challel').html(``);
              $('#success_dashboard').html(`<div class="form-group">
                   <label>Select Payment Channel</label>
                   <select name="response['payment_mode']" class="form-control form-control-sm" id="payment_channel">
                     <option value="">Select</option>
                     <?php foreach ($payment_channel as $channel) {
                            echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                        } ?>
                   </select>
                   <span id="payment_channel_msg" class="custom-text-danger"></span>
                 </div>
                 <div class="form-group">
                                <label>UTR/Transaction</label>
                                <input type="text" placeholder="UTR/Transaction" id="utr" name="response['utr_transaction']" class="form-control form-control-sm">
                                <span id="utr_transaction_msg" class="custom-text-danger"></span>
                            </div>`);
          } else if (status == 'rejected') {
            $('#challel').html(``);
              $('#success_dashboard').html(``);
          } else {
            $('#challel').html(``);
              $('#success_dashboard').html(`<div class="form-group">
                   <label>Select Payment Channel</label>
                   <select name="response['payment_mode']" class="form-control form-control-sm" id="payment_channel">
                     <option value="">Select</option>
                     <?php foreach ($payment_channel as $channel) {
                            echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                        } ?>
                   </select>
                   <span id="payment_channel_msg" class="custom-text-danger"></span>
                 </div>`);
          }
      })




//show transaction detils
    $(document).on('click', '.view_dashboard', function() {
        var _id = $(this).attr('_id');
        $.ajax({
            url: "<?= url('employee/a-trans-detail') ?>",
            data: {
                'id': _id,
            },
            type: 'GET',
            dataType: "json",
            success: function(res) {

                $('#details_dashboard').html(res);
                $('#view_modal_dashboard').modal('show');
            }
        })
    });

    $(document).on('change', '#status-select-dashboard', function(e) {
        e.preventDefault();

        var type = $(this).val();

        if (type == '') {
            $('#comment-field_dashboard').hide();
        } else {
            $.ajax({
                url: "<?= url('employee/a-trans-comment') ?>",
                data: {
                    'type': type
                },
                type: 'GET',
                dataType: "json",
                success: function(res) {
                    $('#comment-field_dashboard').show();
                    $('#comment_dashboard').html(res);
                }
            })
        }
    })

    /*start form submit functionality*/
    $("form#approve_trans_dashboard").submit(function(e) {
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
                    $('form#approve_trans_dashboard')[0].reset();
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
        $("#details1_dashboard").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $(copy).removeClass('d-none');
        $temp.remove();
    }
</script>

@endpush
<!--end retailer transer module-->
@endsection