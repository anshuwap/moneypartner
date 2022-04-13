@extends('retailer.layouts.app')
@section('content')
@section('page_heading', 'Spent Amount Topup List')


<div class="row">

    <div class="col-12 mt-2">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">Passbook</h3>

                <div class="card-tools">
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" id="create_topup"><i class="fas fa-hand-holding-usd"></i>&nbsp;Request for Topup</a>
  <a href="{{ url('retailer/passbook-export') }}{{ !empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''}}" class="btn btn-sm btn-success"><i class="fas fa-cloud-download-alt"></i>&nbsp;Export</a>
                    @if(!empty($filter))
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
                    @else
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
                    @endif

                </div>
            </div>


            <div class="row pl-2 pr-2" id="filter" <?=(empty($filter))?"style='display:none'":""?>>
                <div class="col-md-12 ml-auto">
                    <form action="{{ url('retailer/passbook') }}">
                        <div class="form-row">
                             <div class="form-group col-md-2">
                                <label>Start Data</label>
                                <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" name="start_date" />
                            </div>

                            <div class="form-group col-md-2">
                                <label>End Data</label>
                                <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" name="end_date" id="end-date" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Type</label>
                                <select class="form-control form-control-sm" name="type">
                                    <option value="">All</option>
                                    <option value="credit" <?= (!empty($filter['type']) && $filter['type']=='credit')?'selected':'' ?>>Credit</option>
                                    <option value="debit" <?= (!empty($filter['type']) && $filter['type']=='debit')?'selected':'' ?>>Debit</option>
                                </select>
                            </div>
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                <a href="{{ url('retailer/passbook') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
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

<div class="modal fade" id="add_topup_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_totup">Request For Topup</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body">
                <form id="add_topup_id" action="{{ url('retailer/topup') }}" method="post">
                    @csrf
                    <div id="put"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Payment Mode</label>
                                <select class="form-control form-control-sm" required id="payment_mode" name="payment_mode">
                                    <option value="">Select</option>
                                    <option value="bank_account">Bank Account</option>
                                    <option value="upi_id">UPI ID</option>
                                    <option value="qr_code">QR Code</option>
                                </select>
                                <span id="payment_mode_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label>Select</label>
                                <select class="form-control form-control-sm" required disabled id="payment_reference" name="payment_reference_id">
                                    <option>Select</option>
                                </select>
                                <span id="payment_reference_id_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="" id="show-paymnet-details">
                            </div>


                            <div class="form-group">
                                <label>Amount</label>
                                <input type="number" placeholder="Enter Amount" id="name" required name="amount" class="form-control form-control-sm">
                                <span id="amount_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label>Comment</label>
                                <textarea class="form-control" name="comment" id="comment" rows="5"></textarea>
                                <span id="comment_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label>Uploade Screenshot</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="attachment" class="custom-file-input custom-file-input-sm" id="attachment">
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                </div>
                                <span id="attachment_msg" class="custom-text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label>Payment Data & Time</label>
                                <input type="datetime-local" id="payment_date" name="payment_date" class="form-control form-control-sm datetimepicker-input" data-target="#reservationdatetime" value="<?= date('Y-m-d\TH:i') ?>" min="<?= date('Y-m-d\TH:i') ?>" max="2030-06-14T00:00">
                            </div>

                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-success btn-sm" id="submit_topup_id" value="Submit">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $('#payment_mode').change(function() {
        var payment_mode = $(this).val();
        $.ajax({
            url: "{{ url('retailer/outlet-payment-mode') }}",
            data: {
                'payment_mode': payment_mode
            },
            type: 'GET',
            dataType: 'JSON',
            success: function(res) {
                $('#payment_reference').removeAttr('disabled');
                $('#payment_reference').html(res);
            }
        })
    });

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

    $('#create_topup').click(function(e) {

        e.preventDefault();

        $('form#add_topup_id')[0].reset();

        let url = '{{ url("retailer/topup") }}';

        $('#heading_topup').html('Request For Topup');

        $('#put').html('');

        $('form#add_topup_id').attr('action', url);

        $('#submit_topup_id').val('Submit');

        $('#add_topup_modal').modal('show');

    })


    $(document).on('click', '.edit_topup_id', function(e) {
        e.preventDefault();
        var id = $(this).attr('topup_id_id');
        var url = "{{ url('retailer/topup') }}/" + id + "/edit";
        $.ajax({
            url: url,
            method: 'GET',
            dataType: "JSON",
            data: {
                id: id,
            },
            success: function(res) {
                // var url = "{{ asset('attachment/payment_mode/')}}/" + res.topup_id;
                $('#name').val(res.name);
                $('#topup_id').val(res.topup_id);
                $('#status').val(res.status);
                let urlU = '{{ url("retailer/topup") }}/' + id;
                $('#heading_topup').html('Edit topup ID');
                $('#put').html('<input type="hidden" name="_method" value="PUT">');
                $('form#add_topup_id').attr('action', urlU);
                $('#submit_topup_id').val('Update');
                $('#add_topup_modal').modal('show');
            },

            error: function(error) {
                console.log(error)
            }
        });
    });



    /*start form submit functionality*/

    $("form#add_topup_id").submit(function(e) {

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
                    $('form#add_topup_id')[0].reset();
                    setTimeout(function() {
                        window.location.href = "{{ url('retailer/topup-history')}}";

                    }, 1000)
                }
            }
        });
    });
    /*end form submit functionality*/
</script>


<!-- Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Import Csv File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Download sample lead Import(CSV) file : <a href="{{ url('admin/order-sample') }}" class="text-green">Download</a></p>
                <form id="import_form" action="{{ url('admin/outlet-import') }}" method="post">
                    @csrf

                    <div class="form-row">
                        <div class="form-group col-md-10">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input custom-file-input-sm" id="imgInp" accept=".csv">
                                    <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                </div>
                            </div>
                            <span id="file_msg" class="custom-text-danger"></span>
                        </div>

                        <div class="form-group col-md-2">
                            <input type="submit" class="btn btn-success btn-sm" id="submit_bank_charges" value="Import">
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('#import').click(function(e) {
        e.preventDefault();
        $('form#import_form')[0].reset();
        let url = '{{ url("admin/outlet-import") }}';
        $('form#import_form').attr('action', url);
        $('#importModal').modal('show');
    })


    /*start form submit functionality*/
    $("form#add_bank_charges").submit(function(e) {
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
                $('.has-loader').addClass('has-loader-active');
            },
            success: function(res) {
                //hide loader
                $('.has-loader').removeClass('has-loader-active');

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
                    $('form#add_bank_charges')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 2000)

                }
            }
        });
    });

    /*end form submit functionality*/
</script>

@endpush

@endsection