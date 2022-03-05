     <div class="row">
         <div class="col-12 mt-2">
             <div class="card">

                 <div class="card-header">
                     <h3 class="card-title">Transaction List</h3>
                     <div class="card-tools">
                         <a href="javascript:void(0);" class="btn btn-sm btn-success" id="create_customer"><i class="fas fa-plus-circle"></i>&nbsp;Add DMT</a>
                         <a href="javascript:void(0);" class="btn btn-sm bg-fuchsia color-palette" id="create_payout"><i class="fas fa-plus-circle"></i>&nbsp;Add Payout</a>
                         <a href="javascript:void(0);" id="import" class="btn btn-sm btn-warning"><i class="fas fa-cloud-upload-alt"></i>&nbsp;Import</a>
                     </div>
                 </div>

                 <!-- /.card-header -->
                 <div class="card-body table-responsive py-4 table-sm">
                     <table id="table" class="table table-hover text-nowrap">
                         <thead>
                             <tr>
                                 <th>Sr No.</th>
                                 <th>Customer</th>
                                 <th>Transaction Id</th>
                                 <th>Mode</th>
                                 <th>Amount</th>
                                 <th>Beneficiary</th>
                                 <th>IFSC</th>
                                 <th>Account No.</th>
                                 <th>Bank Name</th>
                                 <th>Status</th>
                                 <th>Datetime</th>

                             </tr>
                         </thead>
                         <tbody>
                             @foreach($transactions as $key=>$trans)
                             <?php

                                $payment = (object)$trans->payment_channel;

                                if ($trans->status == 'success') {
                                    $status = '<strong class="text-success">' . ucwords($trans->status) . '</strong>';
                                    $action = '-';
                                } else if ($trans->status == 'rejected') {
                                    $status = '<strong class="text-danger">' . ucwords($trans->status) . '</strong>';
                                    $action = '-';
                                } else {

                                    $status = '<span class="tag-small-warning">' . ucwords($trans->status) . '</span>';
                                } ?>
                             <tr>
                                 <td>{{ ++$key }}</td>
                                  <td><div style="display: grid;"><span>{{ ucwords($trans->sender_name)}}</span><span style="font-size: 13px;">{{ $trans->mobile_number }}</span></div></td>
                                 <td>{{ $trans->transaction_id }}</td>
                                 <td><span class="tag-small">{{ ucwords(str_replace('_',' ',$trans->type)) }}</span></td>
                                 <td>{!! mSign($trans->amount) !!}</td>
                                 <td>{{ ucwords($trans->receiver_name)}}</td>
                                 <!-- <td>{{ ucwords(str_replace('_',' ',$trans->payment_mode))}}</td> -->
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

             @include('retailer.transaction.dmt')
             @include('retailer.transaction.payout')
         </div>
     </div>