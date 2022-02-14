@extends('admin.layouts.app')

@section('content')
@section('page_heading', 'Dashboard')

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Dashboard</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon bg-info elevation-1"><i class="fas fa-store"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Outlets</span>
            <span class="info-box-number">
              {{ $total_outlet }}
            </span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-wallet"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Topup Amount</span>
            <span class="info-box-number">{!!mSign($total_topup_amount)!!}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->

      <!-- fix for small devices only -->
      <div class="clearfix hidden-md-up"></div>

      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill-alt"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">DMT Amount</span>
            <span class="info-box-number">{!!mSign($current_month_dmt_amount + $current_month_bulk_amount)!!}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-money-bill-wave"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Total DMT Amount</span>
            <span class="info-box-number">{!!mSign($total_bulk_amount + $total_dmt_amount)!!}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->


    <!-- Topup Request List -->
    <div class="card direct-chat direct-chat-primary">
      <div class="card-header">
        <h3 class="card-title">Topup Request</h3>

      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <!-- Conversations are loaded here -->
        <div class="direct-chat-messages">
          <table class="table table-hover text-nowrap table-sm">

            <thead>
              <tr>
                <th>Sr No.</th>
                <th>Retailer Name</th>
                <th>Amount</th>
                <th>Payment Mode</th>
                <th>Payment Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>

            <tbody>
              @php
              $i =0;
              @endphp
              @if(!empty($topup_request))

              @foreach($topup_request as $key=>$topup)
              <tr>
                <td>{{ ++$i }}</td>
                <td><a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="{{ $topup->comment }}">{{ $topup->retailer_name }}</a></td>
                <td>{!! mSign($topup->amount) !!}</td>
                <td>{{ $topup->payment_mode }}</td>
                <td>{{ $topup->payment_date }}</td>
                <td id="status-{{ $topup->id }}">
                  {{ $topup->status }}
                </td>
                <td>
                  <div id="action-{{$topup->id}}">
                    <a href="javascript:void(0);" class="text-success view-topup-request" topup_id="{{ $topup->id }}" data-toggle="tooltip" data-placement="bottom" title="View Details"><i class="fas fa-eye"></i></i></a>&nbsp;
                    <a href="javascript:void(0);" class="text-ingfo add-topup-request" topup_id="{{ $topup->id }}" data-toggle="tooltip" data-placement="bottom" title="Approve Topup"><i class="fas fa-plus-circle"></i></a>
                  </div>
                </td>
              </tr>
              @endforeach
              @else
            <tbody>
              <tr>
                <td colspan="7" style="text-align:center;">There is no any Topup Request</td>
              </tr>
            </tbody>
            @endif
            </tbody>

          </table>
        </div>
      </div>
    </div>


    <!-- Topup Request List -->

    <div class="card card-primary card-outline card-outline-tabs">
      <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="false">DMT Transfer</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Payout Transfer</a>
          </li>

        </ul>
      </div>
      <div class="pl-2 pr-2" style="height: 300px; overflow-y: scroll;">
        <div class="tab-content" id="custom-tabs-four-tabContent">
          <div class="tab-pane active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
 <table id="table" class="table table-hover text-nowrap table-sm">
              <thead>
                <tr>
                  <th>Sr. No.</th>
                  <th>Total Amount</th>
                  <th>Beneficiary Name</th>
                  <th>IFSC</th>
                  <th>Account No./UPI Id</th>
                  <th>Bank Name</th>
                  <th>Status</th>
                  <th>Datetime</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if(!$customer_trans->isEmpty())
                @foreach($customer_trans as $key=>$trans)
                <?php
                if (!empty($trans->trans_details)) {
                  $i = 0;
                  foreach ($trans->trans_details as $ke => $detail) {
                  if ($detail['status'] == 'pending') {
                    $payment = (object)$detail['payment_channel'];

                    if ($detail['status'] == 'approved') {
                      $status = '<strong class="text-success">' . ucwords($detail['status']) . '</strong>';
                    } else if ($detail['status'] == 'rejected') {
                      $status = '<strong class="text-danger">' . ucwords($detail['status']) . '</strong>';
                    } else {
                      $status = '<strong class="text-warning">' . ucwords($detail['status']) . '</strong>';
                    }
                ?>
                    <tr>
                      <td>{{ ++$ke }}</td>

                      <td>{!! mSign($detail['amount'] + $detail['transaction_fees']) !!}</td>
                      <td>{{ ucwords($detail['receiver_name'] ) }}</td>
                      <td>{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</td>
                      <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
                        <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
                      </td>
                      <td><?= (!empty($payment->bank_name)) ? $payment->bank_name : '-' ?></td>
                      <td>{!! $status !!}</td>
                      <td>{{ date('d,M y H:i A',$detail['created'])}}</td>
                      <td>
                        <a href="javascript:void(0);" class="btn btn-info btn-sm view" trans_id="{{ $trans->_id }}" _id="{{ $i }}"><i class="fas fa-eye"></i>&nbsp;view</a>
                        @if(empty($detail['admin_action']))
                        <a href="javascript:void(0);" class="btn btn-danger btn-sm customer_trans" trans_id="{{ $trans->_id }}" _id="{{ $i }}">Action</a>
                        @endif
                      </td>
                    </tr>
                <?php $i++;
                  }}
                } ?>
              <tbody>
                @endforeach
                @else
                <tr>
                  <td colspan="7" style="text-align:center;">There is no any Topup Request</td>
                </tr>
              </tbody>
              @endif
              </tbody>
            </table>
          </div>
          <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">
            <table id="table" class="table table-hover text-nowrap table-sm">
              <thead>
                <tr>
                  <th>Sr. No.</th>
                  <th>Total Amount</th>
                  <th>Beneficiary Name</th>
                  <th>IFSC</th>
                  <th>Account No./UPI Id</th>
                  <th>Bank Name</th>
                  <th>Status</th>
                  <th>Datetime</th>
                  <th>Action</th>
                </tr>
              </thead>

                @if(!empty($retailerTrans))
                <tbody>
                        @foreach($retailerTrans as $key=>$trans)
                        <?php

                        $payment = (object)$trans->payment_channel;

                        if ($trans->status == 'approved') {
                            $status = '<strong class="text-success">' . ucwords($trans->status) . '</strong>';
                            $action = '-';
                        } else if ($trans->status == 'rejected') {
                            $status = '<strong class="text-danger">' . ucwords($trans->status) . '</strong>';
                            $action = '-';
                        } else {

                            $status = '<strong class="text-warning">' . ucwords($trans->status) . '</strong>';
                            $action = '<a href="javascript:void(0);" class="btn btn-danger btn-sm retailer_trans" _id="'. $trans->_id .'">Action</a>';
                        } ?>
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{!! mSign($trans->amount + $trans->transaction_fees) !!}</td>
                            <td>{{ ucwords($trans->receiver_name)}}</td>
                            <td>{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</td>
                            <td><?= (!empty($payment->account_number))?$payment->account_number:''?>
                            <?= (!empty($payment->upi_id))?$payment->upi_id:'' ?>
                            </td>
                            <td><?= (!empty($payment->bank_name))?$payment->bank_name:'-' ?></td>
                            <td>{!! $status !!}</td>
                            <td>{{ date('d,M y H:i A',$trans->created) }}</td>
                            <td> <a href="javascript:void(0);" class="btn btn-info btn-sm view_dashboard" _id="{{ $trans->_id }}"><i class="fas fa-eye"></i>&nbsp;view</a>
                                {!! $action !!}</td>
                        </tr>
                        @endforeach
                @else
                <tr>
                  <td colspan="7" style="text-align:center;">There is no any Topup Request</td>
                </tr>
              @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Main row -->
    <div class="row">
      <!-- Left col -->
      <section class="col-lg-7 connectedSortable">

        <!-- BAR CHART -->
        <div class="card card-success">
          <div class="card-header">
            <h3 class="card-title">Bar Chart</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="chart">
              <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->


      </section>
      <!-- /.Left col -->
      <!-- right col (We are only adding the ID to make the widgets sortable)-->
      <section class="col-lg-5 connectedSortable">

        <div class="card" style="height: 338px;">
          <div class="card-header">
            <h3 class="card-title">Statics</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
              <div class="col-md-8">
                <div class="chart-responsive">
                  <div class="chartjs-size-monitor">
                    <div class="chartjs-size-monitor-expand">
                      <div class=""></div>
                    </div>
                    <div class="chartjs-size-monitor-shrink">
                      <div class=""></div>
                    </div>
                  </div>
                  <canvas id="pieChart" height="147" width="296" style="display: block; height: 118px; width: 237px;" class="chartjs-render-monitor"></canvas>
                </div>
                <!-- ./chart-responsive -->
              </div>
              <!-- /.col -->
              <div class="col-md-4">
                <ul class="chart-legend clearfix">
                  <li><i class="far fa-circle text-danger"></i> Topup Amount</li>
                  <li><i class="far fa-circle text-success"></i> DMT Amount</li>
                  <li><i class="far fa-circle text-warning"></i> Total DMT Amount</li>
                  <li><i class="far fa-circle text-info"></i> Outlets</li>

                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>

        </div>

        <!-- /.card -->
      </section>
      <!-- right col -->
    </div>
    <!-- /.row (main row) -->


    <!--/.direct-chat -->
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

@push('custom-script')


<!-- Modal -->
<div class="modal fade" id="addTopup-request" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Place A Comment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form action="{{ url('admin/topup-request') }}" id="topup-request" method="post">

          <input type="hidden" name="id" id="topup_id" value="">
          <div class="form-group">
            <select name="status" class="form-control control-sm" required='required'>
              <option value="">Select</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          <div class="form-group">
            <textarea name="comment" rqueired="required" id="comment-" class="form-control" rows="5" placeholder="Enter Comment Here"></textarea>
          </div>
          <div class="form-group">
            <input type="submit" class="btn-sm btn btn-success" id="submit_btn" value="Submit">
            <a class="btn-sm btn btn-danger" id="cancel">Cancel</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="topup-request-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Topup Request Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="dataVal">

        </div>
       <div id="topup-form">
          <a href="javascript:void(0)" class="btn-sm btn-info" id="action1">Action</a>
          <div class="row" id="placeComment" style="display: none;">
            <div class="col-md-12 border mt-2">
              <form action="{{ url('admin/topup-request') }}" id="topup-request" method="post">
                <div class="tooltip-title">
                  <h6>Place a Comment</h6>
                </div>
                <input type="hidden" name="id" id="topup_id" value="">
                <div class="form-group">
                  <select name="status" class="form-control control-sm" required='required'>
                    <option value="">Select</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                  </select>
                </div>
                <div class="form-group">
                  <textarea name="comment" rqueired="required" id="comment-" class="form-control" rows="5" placeholder="Enter Comment Here"></textarea>
                </div>
                <div class="form-group">
                  <input type="submit" class="btn-sm btn btn-success" value="Submit">
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {

    /*start form submit functionality*/
    $("form#topup-request").submit(function(e) {
      e.preventDefault();
      var id = $('#topup_id').val();
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
          $('#submit_btn').val('Submitting...');
        },
        success: function(res) {
          //hide loader
          $('.has-loader').removeClass('has-loader-active');
          $('#submit_btn').val('Submit');


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
            $('#status-' + res.id).html(res.status_msg);
            $('form#topup-request')[0].reset();
            $('#action-' + id).remove();
          }
        }
      });
    });
    /*end form submit functionality*/


    $('.view-topup-request').click(function() {
      var topup_id = $(this).attr('topup_id');
      $('#topup_id').val(topup_id);
      $.ajax({
        url: "{{ url('admin/topup-request-details') }}/" + topup_id,
        type: 'GET',
        dataType: 'JSON',
        success: function(res) {
          $('#dataVal').html(res.data);
           $('#topup-form').show();
          if (res.show_action)
            $('#topup-form').hide();
          $('#topup-request-details').modal('show');
        }
      })
    })

    $('.add-topup-request').click(function() {
      var topup_id = $(this).attr('topup_id');
      $('#topup_id').val(topup_id);
      $('#addTopup-request').modal('show');
    })

  });


  // Get context with jQuery - using jQuery's .get() method.
  var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
  var pieData = {
    labels: [
      'Topup Amount',
      'DMT Amount',
      'Total DMT Amount',
      'Outlet'

    ],
    datasets: [{
      data: [<?= $total_topup_amount ?>, <?= $current_month_dmt_amount + $current_month_bulk_amount ?>, <?= $current_month_dmt_amount + $current_month_bulk_amount ?>, <?= $total_outlet ?>],
      backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef']
    }]
  }
  var pieOptions = {
    legend: {
      display: false
    }
  }
  // Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  // eslint-disable-next-line no-unused-vars
  var pieChart = new Chart(pieChartCanvas, {
    type: 'doughnut',
    data: pieData,
    options: pieOptions
  })

  //-----------------
  // - END PIE CHART -
  //-----------------
  //-------------
  //-------------

  var areaChartData = {
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    datasets: [{
        label: 'Topup Amount',
        backgroundColor: 'rgba(47,194,150,1)',
        borderColor: 'rgba(60,141,188,0.8)',
        pointRadius: false,
        pointColor: '#3b8bba',
        pointStrokeColor: 'rgba(60,141,188,1)',
        pointHighlightFill: '#fff',
        pointHighlightStroke: 'rgba(60,141,188,1)',
        data: [28, 48, 40, 19, 86, 27, 90, 20, 10, 20, 10, 11]
      },
      {
        label: 'DMT Amount',
        backgroundColor: 'rgba(255,165,0)',
        borderColor: 'rgba(255,165,0)',
        pointRadius: false,
        pointColor: 'rgba(255,165,0)',
        pointStrokeColor: '#c1c7d1',
        pointHighlightFill: '#fff',
        pointHighlightStroke: 'rgba(220,220,220,1)',
        data: [65, 59, 80, 81, 56, 55, 40, 90, 60, 20, 90, 88]
      }

    ]
  }

  //- BAR CHART -
  //-------------
  var barChartCanvas = $('#barChart').get(0).getContext('2d')
  var barChartData = $.extend(true, {}, areaChartData)
  var temp0 = areaChartData.datasets[0]
  var temp1 = areaChartData.datasets[1]
  barChartData.datasets[0] = temp1
  barChartData.datasets[1] = temp0

  var barChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    datasetFill: false
  }

  new Chart(barChartCanvas, {
    type: 'bar',
    data: barChartData,
    options: barChartOptions
  })
</script>

@endpush


<!--start retailer transfer module-->

@push('modal')

<!-- Modal -->
<div class="modal fade" id="approve_modal_dashboard" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="heading_bank_dashboard">Approved/Reject Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="cover-loader-modal d-none">
                <div class="loader-modal"></div>
            </div>

            <div class="modal-body">
                <form id="approve_trans_dashboard" action="{{ url('admin/a-retailer-trans') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="trans_id_dahboard" name="trans_id">
                            <input type="hidden" id="key" name="key">

                            <div class="form-group">
                                <label>Action</label>
                                <select name="status" id="status-select-dashboard" class="form-control form-control-sm">
                                    <option value="">Select</option>
                                    <option value="approved">Approved</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                <span id="status_msg" class="custom-text-danger"></span>
                            </div>

                            <div id="approved_dashboard"></div>

                            <div class="form-group">
                                <label>Select Payment Channel</label>
                                <select name="admin_action['payment_mode']" class="form-control form-control-sm" id="payment_channel">
                                    <option value="">Select</option>
                                    <?php foreach ($payment_channel as $channel) {
                                        echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                                    } ?>
                                </select>
                                <span id="payment_channel_msg" class="custom-text-danger"></span>
                            </div>

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
        $('#approve_modal_dashboard').modal('show');
    })


    //show transaction detils
    $(document).on('click', '.view_dashboard', function() {
        var _id = $(this).attr('_id');
        $.ajax({
            url: "<?= url('admin/a-retailer-detail') ?>",
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


    $('#status-select-dashboard').change(() => {
        let status = $('#status-select-dashboard').val();
        if (status == 'approved') {
            $('#approved_dashboard').html(`<div class="form-group">
                                <label>UTR/Transaction</label>
                                <input type="text" placeholder="UTR/Transaction" id="utr" name="admin_action['utr_transaction']" class="form-control form-control-sm">
                                <span id="utr_transaction_msg" class="custom-text-danger"></span>
                            </div>`);
        } else {
            $('#approved_dashboard').html(``);
        }
    })


    $('#status-select-dashboard').change(function(e) {
        e.preventDefault();

        var type = $(this).val();

        if (type == '') {
            $('#comment-field_dashboard').hide();
        } else {
            $.ajax({
                url: "<?= url('admin/a-retailer-comment') ?>",
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

<!--start customer module-->

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
                <form id="approve_trans" action="{{ url('admin/a-customer-trans') }}" method="post">
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

                            <div class="form-group">
                                <label>Select Payment Channel</label>
                                <select name="admin_action['payment_mode']" class="form-control form-control-sm" id="payment_channel">
                                    <option value="">Select</option>
                                    <?php foreach ($payment_channel as $channel) {
                                        echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                                    } ?>
                                </select>
                                <span id="payment_channel_msg" class="custom-text-danger"></span>
                            </div>

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
    $(document).on('click', '.customer_trans', function(e) {
        e.preventDefault();

        $('#trans_id').val($(this).attr('trans_id'));
        $('#key').val($(this).attr('_id'));
        $('#approve_modal').modal('show');
    });

    $('#status-select').change(() => {
        let status = $('#status-select').val();
        if (status == 'approved') {
            $('#approved').html(`<div class="form-group">
                                <label>UTR/Transaction</label>
                                <input type="text" placeholder="UTR/Transaction" id="utr" name="admin_action['utr_transaction']" class="form-control form-control-sm">
                                <span id="utr_transaction_msg" class="custom-text-danger"></span>
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
                url: "<?= url('admin/a-customer-comment') ?>",
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


    //show transaction detils
    $(document).on('click', '.view', function() {
        var trnas_id = $(this).attr('trans_id');
        var key = $(this).attr('_id');
        $.ajax({
            url: "<?= url('admin/a-view-detail') ?>",
            data: {
                'id': trnas_id,
                'key': key
            },
            type: 'GET',
            dataType: "json",
            success: function(res) {

                $('#details').html(res);
                $('#view_modal').modal('show');
            }
        })
    });


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
<!--end customer module-->
@endsection