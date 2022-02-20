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
    <div class="card card-primary card-outline card-outline-tabs">
      <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">

          <li class="nav-item">
            <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="false"><i class="fas fa-file-invoice-dollar"></i>&nbsp;DMT Transfer</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false"><i class="fas fa-money-check nav-icon"></i>&nbsp;Payout Transfer</a>
          </li>
         <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-profile-tab1" data-toggle="pill" href="#custom-tabs-four-profile1" role="tab" aria-controls="custom-tabs-four-profile1" aria-selected="false"><i class="fas fa-hand-holding-usd"></i> &nbsp;Offline Payout</a>
          </li>
        </ul>
      </div>
      <div class="pl-2 pr-2" style="height: 300px; overflow-y: scroll;">
        <div class="tab-content" id="custom-tabs-four-tabContent">
          <div class="tab-pane active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
            @include('admin.dashboard.dmt_transaction')

          </div>
          <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">
            @include('admin.dashboard.payout_transaction')

          </div>

          <div class="tab-pane fade" id="custom-tabs-four-profile1" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab1">
            @include('admin.dashboard.offline_payout_transaction')

          </div>
        </div>
      </div>
    </div>

      @include('admin.dashboard.topup_request')

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


<script>
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

@endsection