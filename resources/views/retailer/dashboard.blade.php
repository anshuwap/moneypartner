@extends('retailer.layouts.app')

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

    <div class="row">
      <div class="col-12 col-sm-6 col-md-3">
        <div class="pl-3 p-2 card" style="height: 161px;">
          <div class="row">
            <div class="col-md-12">
              <div>Account Balance</div>
              <div>
                {!!mSign(Auth::user()->available_amount)!!}
              </div>
            </div>
          </div>


        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box" style="height: 161px;">
          <div class="info-box-content">
            <div>
              <span>Transfer funds to the following to use here.</span>
            </div>
            <div class="mb-1">
              <span>Outlat Name :</span>&nbsp;&nbsp;&nbsp;<span class="font-weight-bold">{{ ucwords(Auth::user()->outlet_name) }}</span>
            </div>

            <div class="" style="line-height: 20px; font-size:13px">
              <table>
                @foreach($upis as $key=>$upi)
                <tr>
                  <td>UPI Id.</td>
                  <th><span id="text-copy-{{$key}}">{{ $upi->upi_id }}</span></th>&nbsp;&nbsp;
                  <td class="ml-2">
                    <span class="ml-1"><a href="javascript:void(0);" onClick="copyToClipboard('#text-copy-{{$key}}','#copy-copy-{{$key}}')" class="text-danger"><i class="fas fa-copy"></i></a></span>
                    <span class="ml-4 d-none" id="copy-copy-{{$key}}"><i class="fas fa-check-circle text-success"></i>Copied</span>
                  </td>
                </tr>
                @endforeach
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-5">
        <div class="info-box">
          <div class="info-box-content">
            <div class="row mb-1">
              <div class="col-md-7">
                <span>Transfer funds to the following to use here.</span>
              </div>
              <div class="col-md-5">
                <span>Outlat Name :</span>&nbsp;&nbsp;&nbsp;<span class="font-weight-bold">{{ ucwords(Auth::user()->outlet_name) }}</span>
              </div>
            </div>


            <div class="row">
              @foreach($bank_accounts as $key=>$account)
              <?php if ($key > 1) {
                $show = '<div class="col-md-12 float-right"><a href="javascript:void(0);" class="text-info" id="show-more">Show More</a></div>';
                $class = 'show-more mt-1';
                $style = 'display:none';
              } else {
                $show = '';
                $class = '';
                $style = '';
              } ?>
              <div class="col-md-6 {{ $class}}" style="line-height: 20px; font-size:12px; <?=$style?>">
                <table>
                  <tr>
                    <td>Ac. Holder</td>
                    <td><span id="text1-{{$key}}">{{ ucwords($account->account_holder_name)}}</span></td>
                    <td>
                      <span><a href="javascript:void(0);" onClick="copyToClipboard('#text0-{{$key}}','#copy0-{{$key}}')" class="text-danger"><i class="fas fa-copy"></i></a></span>
                      <span class="ml-4 d-none" id="copy0-{{$key}}"><i class="fas fa-check-circle text-success"></i>Copied</span>
                    </td>
                  </tr>
                  <tr>
                    <td>Bank Name.</td>
                    <td><span id="text1-{{$key}}">{{ $account->bank_name}}</span></td>
                    <td>
                      <span><a href="javascript:void(0);" onClick="copyToClipboard('#text1-{{$key}}','#copy1-{{$key}}')" class="text-danger"><i class="fas fa-copy"></i></a></span>
                      <span class="ml-4 d-none" id="copy1-{{$key}}"><i class="fas fa-check-circle text-success"></i>Copied</span>
                    </td>
                  </tr>
                  <tr>
                    <td>A/C No.</td>
                    <th><span id="text2-{{$key}}">{{ $account->account_number}}</span></th>
                    <td>
                      <span><a href="javascript:void(0);" onClick="copyToClipboard('#text2-{{$key}}','#copy2-{{$key}}')" class="text-danger"><i class="fas fa-copy"></i></a></span>
                      <span class="ml-4 d-none" id="copy2-{{$key}}"><i class="fas fa-check-circle text-success"></i>Copied</span>
                    </td>
                  </tr>
                  <tr>
                    <td>IFSC.</td>
                    <th><span id="text3-{{$key}}">{{ $account->ifsc_code }}</span></th>
                    <td>
                      <span><a href="javascript:void(0);" onClick="copyToClipboard('#text3-{{$key}}','#copy3-{{$key}}')" class="text-danger"><i class="fas fa-copy"></i></a></span>
                      <span class="ml-4 d-none" id="copy3-{{$key}}"><i class="fas fa-check-circle text-success"></i>Copied</span>
                    </td>
                  </tr>
                  <!-- <tr>
                    <td>Mode.</td>
                    <td>IMPS/NEFT/RTGS</td>
                  </tr> -->
                </table>
              </div>
              {!! $show !!}
              @endforeach

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- <div class="row">
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon bg-info elevation-1"><i class="fas fa-wallet"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Topup Amount</span>
            <span class="info-box-number">
              10
            </span>
          </div>

        </div>

      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-money-check"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Spent Amount</span>
            <span class="info-box-number">41,410</span>
          </div>
        </div>

      </div>

      <div class="clearfix hidden-md-up"></div>

      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill-alt"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">DMT Transfer Amount</span>
            <span class="info-box-number">760</span>
          </div>

        </div>

      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-money-bill-wave"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Blance Amount</span>
            <span class="info-box-number">2,000</span>
          </div>

        </div>

      </div>

    </div> -->

    @include('retailer.dashboard.transaction')

    <!-- Topup Request List -->
    @include('retailer.dashboard.topup_request')


    <!-- Main row -->
    <div class="row">

      <!-- <section class="col-lg-7 connectedSortable">

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

        </div>

      </section> -->

      <!-- <section class="col-lg-5 connectedSortable">

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

              </div>

              <div class="col-md-4">
                <ul class="chart-legend clearfix">
                  <li><i class="far fa-circle text-danger"></i> Spent Amount</li>
                  <li><i class="far fa-circle text-success"></i> DMT Amount</li>
                  <li><i class="far fa-circle text-warning"></i> Bulk Amount</li>
                  <li><i class="far fa-circle text-info"></i> Topup Amount</li>

                </ul>
              </div>

            </div>

          </div>

        </div>

      </section> -->

    </div>

  </div>
</section>
<!-- /.content -->

@push('custom-script')

<div id="details1_dashboard"></div>
<script>
  $('#show-more').click(function() {
    $('.show-more').toggle();
  });

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

@endsection