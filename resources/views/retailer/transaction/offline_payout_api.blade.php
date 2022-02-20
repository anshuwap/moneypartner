@extends('retailer.layouts.app')

@section('content')
@section('page_heading', 'Retailer List')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">
            <div class="">
                <ul class="nav nav-tabs" role="tablist">

                    @if(!empty(moneyTransferOption()->dmt_transfer_offline))
                    <li class="nav-item">
                        <a href="{{ url('retailer/customer-trans') }}" class="nav-link "><i class="fas fa-file-invoice-dollar"></i>&nbsp;DMT Transaction</a>
                    </li>
                    @endif

                    @if(!empty(moneyTransferOption()->payout_offline))
                    <li class="nav-item">
                        <a href="{{ url('retailer/retailer-trans') }}" class="nav-link">  <i class="fas fa-money-check nav-icon"></i>&nbsp;Payout Transaction</a>
                    </li>
                    @endif

                    @if(!empty(moneyTransferOption()->payout_offline_api))
                    <li class="nav-item">
                        <a href="{{ url('retailer/offline-payout') }}" class="nav-link active"><i class="fas fa-hand-holding-usd"></i> &nbsp;Payout Offline</a>
                    </li>
                    @endif
                </ul>

            </div>


            <!-- /.card-header -->
            <div class="card-body table-responsive py-4 table-sm">
                <table id="table" class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Transaction Id</th>
                            <th>Amount</th>
                            <th>Beneficiary Name</th>
                            <th>Payment Mode</th>
                            <th>IFSC</th>
                            <th>Account No./UPI Id</th>
                            <th>Bank Name</th>
                            <th>Status</th>
                            <th>Datetime</th>

                        </tr>
                    </thead>
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
                        } ?>
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $trans->transaction_id }}</td>
                            <td>{!! mSign($trans->amount) !!}</td>
                            <td>{{ ucwords($trans->receiver_name)}}</td>
                            <td>{{ ucwords(str_replace('_',' ',$trans->payment_mode))}}</td>
                            <td>{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</td>
                            <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
                                <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
                            </td>
                            <td><?= (!empty($payment->bank_name)) ? $payment->bank_name : '-' ?></td>
                            <td>{!! $status !!}</td>
                            <td>{{ date('d,M y H:i A',$trans->created) }}</td>

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

@endsection