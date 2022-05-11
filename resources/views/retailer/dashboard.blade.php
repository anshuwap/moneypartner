@extends('retailer.layouts.app')

@section('content')
@section('page_heading', 'Dashboard')
<style>
  @media only screen and (max-width: 600px) {
    .h-height {
      height: auto !important;
    }

    .btn-r {
      top: 0px !important;
    }
  }

  .btn-r {
    position: relative;
    text-align: center;
    top: 44px;
  }

  .cu-icon {
    width: 34px !important;
  }

  .info-box .info-box-number {
    margin-top: 0rem !important;
  }

  .info-box {
    min-height: 0px !important;
  }
</style>
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
      <div class="col-12 col-sm-6 col-md-5">
        <div class="pl-3 p-2 card h-height" style="height: 130px;">
          <div class="row">
            <div class="col-md-4">
              <div>Account Balance</div>
              <div>
                <h6><strong>{!!mSign(Auth::user()->available_amount)!!}</strong></h6>
              </div>
            </div>

            @if(!empty(MoneyPartnerOption()->e_collection) && MoneyPartnerOption()->e_collection ==1)
            <div class="col-md-4">
              <div>Settlement Balance</div>
              <div>
                <h6> <strong>{!!mSign($settlement_amount)!!}</strong></h6>
              </div>
            </div>

            <div class="col-md-4">
              <div>Un-Settlement Balance</div>
              <div>
                <h6><strong>{!!mSign($un_settlement_amount)!!}</strong></h6>
              </div>
            </div>
            @endif

          </div>

          <div class="card-tools btn-r">
            @if(!empty(MoneyPartnerOption()->dmt_transfer) && MoneyPartnerOption()->dmt_transfer ==1)
            <a href="javascript:void(0);" class="btn btn-sm btn-success" id="create_customer"><i class="fas fa-plus-circle"></i>&nbsp;Add DMT</a>
            @endif
            @if(!empty(MoneyPartnerOption()->payout) && MoneyPartnerOption()->payout ==1)
            <a href="javascript:void(0);" class="btn btn-sm btn-success" id="create_payout"><i class="fas fa-plus-circle"></i>&nbsp;Add Payout</a>
            @endif
            @if(!empty(MoneyPartnerOption()->bulk_payout) && MoneyPartnerOption()->bulk_payout ==1)
            <a href="javascript:void(0);" id="import" class="btn btn-sm btn-success"><i class="fas fa-cloud-upload-alt"></i>&nbsp;Bulk Upload</a>
            @endif
          </div>
        </div>
      </div>

      <!-- <div class="col-12 col-sm-6 col-md-4">
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
      </div> -->

      <div class="col-12 col-sm-6 col-md-7">
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
              <div class="col-md-6 {{ $class}}" style="line-height: 20px; font-size:12px; <?= $style ?>">
                <table>
                  <tr>
                    <td>Ac. Holder</td>
                    <th><span id="text1-{{$key}}">{{ ucwords($account->account_holder_name)}}</span></th>
                    <td>
                      <span><a href="javascript:void(0);" onClick="copyToClipboard('#text0-{{$key}}','#copy0-{{$key}}')" class="text-success"><i class="fas fa-copy"></i></a></span>
                      <span class="ml-4 d-none" id="copy0-{{$key}}"><i class="fas fa-check-circle text-success"></i>Copied</span>
                    </td>
                  </tr>
                  <tr>
                    <td>Bank Name.</td>
                    <th><span id="text1-{{$key}}">{{ $account->bank_name}}</span></th>
                    <td>
                      <span><a href="javascript:void(0);" onClick="copyToClipboard('#text1-{{$key}}','#copy1-{{$key}}')" class="text-success"><i class="fas fa-copy"></i></a></span>
                      <span class="ml-4 d-none" id="copy1-{{$key}}"><i class="fas fa-check-circle text-success"></i>Copied</span>
                    </td>
                  </tr>
                  <tr>
                    <td>A/C No.</td>
                    <th><span id="text2-{{$key}}">{{ $account->account_number}}</span></th>
                    <td>
                      <span><a href="javascript:void(0);" onClick="copyToClipboard('#text2-{{$key}}','#copy2-{{$key}}')" class="text-success"><i class="fas fa-copy"></i></a></span>
                      <span class="ml-4 d-none" id="copy2-{{$key}}"><i class="fas fa-check-circle text-success"></i>Copied</span>
                    </td>
                  </tr>
                  <tr>
                    <td>IFSC.</td>
                    <th><span id="text3-{{$key}}">{{ $account->ifsc_code }}</span></th>
                    <td>
                      <span><a href="javascript:void(0);" onClick="copyToClipboard('#text3-{{$key}}','#copy3-{{$key}}')" class="text-success"><i class="fas fa-copy"></i></a></span>
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


    <div class="row">
      <div class="col-6 col-sm-6 col-md-2">
        <div class="info-box">
          <span class="info-box-icon cu-icon"><i class="fas fa-wallet text-secondary"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Topup Req. Amount</span>
            <span class="info-box-number">
              {!! !empty($total_topup)?mSign($total_topup):0 !!} || {{ $ac_topup}}
            </span>
          </div>
        </div>
      </div>

      <div class="col-6 col-sm-6 col-md-2">
        <div class="info-box mb-3">
          <span class="info-box-icon cu-icon"><i class="fas fa-hand-holding-usd text-secondary"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Approved Topup</span>
            <span class="info-box-number"> {!! !empty($a_topup)?mSign($a_topup):0 !!} || {{ $apc_topup}}</span>
          </div>
        </div>
      </div>

      <div class="col-6 col-sm-6 col-md-2">
        <div class="info-box">
          <span class="info-box-icon cu-icon"><i class="fas fa-hand-holding-water text-secondary"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Pending Topup</span>
            <span class="info-box-number">
              {!! !empty($p_topup)?mSign($p_topup):0 !!} || {{ $pc_topup}}
            </span>
          </div>
        </div>
      </div>


       <div class="col-6 col-sm-6 col-md-2">
        <div class="info-box mb-3">
          <span class="info-box-icon cu-icon"><i class="fas fa-hand-holding-usd text-secondary"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Rejected Topup</span>
            <span class="info-box-number"> {!! !empty($r_topup)?mSign($r_topup):0 !!} || {{ $rc_topup}}</span>
          </div>
        </div>
      </div>


      <div class="clearfix hidden-md-up"></div>

      <div class="col-6 col-sm-6 col-md-2">
        <div class="info-box mb-3">
          <span class="info-box-icon cu-icon"><i class="fas fa-money-bill-alt text-secondary"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Payout Req. Amount</span>
            <span class="info-box-number">{!! !empty($total_trans)?mSign($total_trans):0 !!} || {{ $ac_trans}}</span>
          </div>
        </div>
      </div>

      <div class="col-6 col-sm-6 col-md-2">
        <div class="info-box mb-3">
          <span class="info-box-icon cu-icon"><i class="fas fa-money-bill-alt text-secondary"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Approved Payout</span>
            <span class="info-box-number">{!! !empty($a_trans)?mSign($a_trans):0 !!} || {{ $apc_trans}}</span>
          </div>
        </div>
      </div>


      <div class="col-6 col-sm-6 col-md-2">
        <div class="info-box mb-3">
          <span class="info-box-icon cu-icon"><i class="fas fa-money-bill-wave text-secondary"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Pending Payout</span>
            <span class="info-box-number">{!! !empty($p_trans)?mSign($p_trans):0 !!} || {{ $pc_trans}}</span>
          </div>
        </div>
      </div>

      <div class="col-6 col-sm-6 col-md-2">
        <div class="info-box mb-3">
          <span class="info-box-icon cu-icon"><i class="fas fa-money-bill-alt text-secondary"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Failed Payout</span>
            <span class="info-box-number">{!! !empty($f_trans)?mSign($f_trans):0 !!} || {{ $fc_trans}}</span>
          </div>
        </div>
      </div>

      <div class="col-6 col-sm-6 col-md-2">
        <div class="info-box mb-3">
          <span class="info-box-icon cu-icon"><i class="fas fa-money-bill-wave text-secondary"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Rejected Payout</span>
            <span class="info-box-number">{!! !empty($r_trans)?mSign($r_trans):0 !!} || {{ $rc_trans}}</span>
          </div>
        </div>
      </div>

    </div>

    @include('retailer.dashboard.transaction')

    <!-- Topup Request List -->
    @include('retailer.dashboard.topup_request')



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