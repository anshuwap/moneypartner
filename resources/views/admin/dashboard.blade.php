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
          <li class="breadcrumb-item active">Dashboard v1</li>
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
            <span class="info-box-number">{!!mSign($total_dmt_amount)!!}</span>
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
            <span class="info-box-text">Bulk Transfer Amount</span>
            <span class="info-box-number">{!!mSign($total_bulk_amount)!!}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
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
            <h3 class="card-title">Browser Usage</h3>

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
                  <li><i class="far fa-circle text-danger"></i> Chrome</li>
                  <li><i class="far fa-circle text-success"></i> IE</li>
                  <li><i class="far fa-circle text-warning"></i> FireFox</li>
                  <li><i class="far fa-circle text-info"></i> Safari</li>
                  <li><i class="far fa-circle text-primary"></i> Opera</li>
                  <li><i class="far fa-circle text-secondary"></i> Navigator</li>
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

    <!-- DIRECT CHAT -->
    <div class="card direct-chat direct-chat-primary">
      <div class="card-header">
        <h3 class="card-title">Topup Request</h3>

      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <!-- Conversations are loaded here -->
        <div class="direct-chat-messages">
          <table id="table" class="table table-hover text-nowrap table-sm">
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
              @if(!empty($topup_request))
              @php
              $i =0;
              @endphp
              @foreach($topup_request as $key=>$topup)
              <tr>
                <td>{{ ++$i }}</td>
                <td><a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="{{ $topup->comment }}">{{ $topup->retailer_name }}</a></td>
                <td>{{ $topup->amount }}</td>
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
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
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
        <div>
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
          $('#dataVal').html(res);
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
      'Chrome',
      'IE',
      'FireFox',
      'Safari',
      'Opera',
      'Navigator'
    ],
    datasets: [{
      data: [700, 500, 400, 600, 300, 100],
      backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de']
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
      labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July','August','September','October','November','December'],
      datasets: [
        {
          label               : 'Topup Amount',
          backgroundColor     : 'rgba(47,194,150,1)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius         : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : [28, 48, 40, 19, 86, 27, 90,20,10,20,10,11]
        },
        {
          label               : 'DMT Amount',
          backgroundColor     : 'rgba(210, 214, 222, 1)',
          borderColor         : 'rgba(210, 214, 222, 1)',
          pointRadius         : false,
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [65, 59, 80, 81, 56, 55, 40,90,60,20,90,88]
        },
        {
          label               : 'Bulk Amount',
          backgroundColor     : 'rgba(255, 0, 0, 1)',
          borderColor         : 'rgba(210, 214, 222, 1)',
          pointRadius         : false,
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [65, 59, 80, 81, 56, 55, 40,80,10,80,90,11]
        },
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
      responsive              : true,
      maintainAspectRatio     : false,
      datasetFill             : false
    }

    new Chart(barChartCanvas, {
      type: 'bar',
      data: barChartData,
      options: barChartOptions
    })
</script>

@endpush

@endsection