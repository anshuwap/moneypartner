     <div class="row">
         <div class="col-12 mt-2">
             <div class="card">

                 <div class="card-header">
                     <h3 class="card-title">Transaction List</h3>
                     <div class="card-tools">


                         @if(!empty($filter))
                         <a href="javascript:void(0);" class="btn btn-sm btn-success" id="filter-btn"><i class="far fa-times-circle"></i>&nbsp;Close</a>
                         @else
                         <a href="javascript:void(0);" class="btn btn-sm btn-success" id="filter-btn"><i class="fas fa-filter"></i>&nbsp;Filter</a>
                         @endif

                     </div>
                 </div>

                 <div class="row pl-2 pr-2" id="filter" <?= (empty($filter)) ? "style='display:none'" : "" ?>>
                     <div class="col-md-12 ml-auto">
                         <form action="{{ url('retailer/dashboard') }}">
                             <div class="form-row">
                                 <div class="form-group col-md-2">
                                     <label>Start Data</label>
                                     <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" name="start_date" />
                                     <!-- <input type="text" class="form-control form-control-sm" value="<?= !empty($filter['date_range']) ? $filter['date_range'] : '' ?>" name="date_range" id="daterange-btn" /> -->
                                 </div>

                                 <div class="form-group col-md-2">
                                     <label>End Data</label>
                                     <input type="date" class="form-control form-control-sm" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" name="end_date" id="end-date" />
                                 </div>

                                 <div class="form-group col-md-2">
                                     <label>Transaction Id</label>
                                     <input type="text" class="form-control form-control-sm" placeholder="Transaction ID" value="<?= !empty($filter['transaction_id']) ? $filter['transaction_id'] : '' ?>" name="transaction_id" id="transaction_id" />
                                 </div>
                                 <div class="form-group col-md-2">
                                     <label>Banficiary</label>
                                     <input type="text" class="form-control form-control-sm" placeholder="Enter Banficiary Name" value="<?= !empty($filter['banficiary']) ? $filter['banficiary'] : '' ?>" name="banficiary" id="banficiary" />
                                 </div>

                                 <div class="form-group col-md-2">
                                     <label>Type</label>
                                     <select class="form-control form-control-sm" name="type">
                                         <option value="">All</option>
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
                                         <option value="pending1" <?= (!empty($filter['status']) && $filter['status'] == 'pending1') ? 'selected' : '' ?>>Pending</option>
                                         <option value="progress" <?= (!empty($filter['status']) && $filter['status'] == 'progress') ? 'selected' : '' ?>>Progress</option>
                                         <option value="reject" <?= (!empty($filter['status']) && $filter['status'] == 'reject') ? 'selected' : '' ?>>Reject</option>
                                     </select>
                                 </div>

                                 <div class="form-group mt-4">
                                     <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-search"></i>&nbsp;Search</button>
                                     <a href="{{ url('retailer/dashboard') }}" class="btn btn-danger btn-sm"><i class="fas fa-eraser"></i>&nbsp;Clear</a>
                                 </div>
                             </div>
                         </form>
                     </div>
                 </div>


                 <!-- /.card-header -->
                 <div class="card-body table-responsive py-4 table-sm">
                     <table id="table" class="table table-hover text-nowrap">
                         <thead>
                             <tr>
                                 <th>Sr No.</th>
                                 <!-- <th>Customer</th> -->
                                 <th>Transaction Id</th>
                                 <!-- <th>Mode</th> -->
                                 <th>Channel</th>
                                 <th>Amount</th>
                                 <th>Beneficiary</th>
                                 <th>IFSC</th>
                                 <th>Account No.</th>
                                 <!-- <th>Bank Name</th> -->
                                 <Th>URT No</Th>
                                 <th>Status</th>
                                 <th>Datetime</th>

                             </tr>
                         </thead>
                         <tbody>
                             @foreach($transactions as $key=>$trans)
                             <?php

                                $payment = (object)$trans->payment_channel;
                                $comment = !empty($trans->response['msg']) ? $trans->response['msg'] : '';

                                if ($trans->status == 'success') {
                                    $status = '<span class="tag-small"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                                    $action = '-';
                                } else if ($trans->status == 'process') {
                                    $status = '<span class="tag-small-purple"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                                    $action = '-';
                                } else if ($trans->status == 'rejected') {
                                    $status = '<span class="tag-small-danger"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                                    $action = '-';
                                // } else if ($trans->status == 'failed') {
                                    // $status = '<span class="tag-small-danger"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                                } else if (!empty($trans->response)) {
                                    $status = '<span class="tag-small-warning"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">Pending</a></span>';
                                } else {
                                    $status = '<span class="tag-small"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">Success</a></span>';
                                } ?>
                             <tr>
                                 <td>{{ ++$key }}</td>
                                 <!-- <td>
                                     <div style="display: grid;"><span>{{ ucwords($trans->sender_name)}}</span><span style="font-size: 13px;">{{ $trans->mobile_number }}</span></div>
                                 </td> -->
                                 <td>{{ $trans->transaction_id }}</td>
                                 <!-- <td><span class="tag-small">{{ ucwords(str_replace('_',' ',$trans->type)) }}</span></td> -->
                                 <td><?= (!empty($trans->response['payment_mode'])) ? $trans->response['payment_mode'] : '-' ?></td>

                                 <td>{!! mSign($trans->amount) !!}</td>
                                 <td>{{ ucwords($trans->receiver_name)}}</td>
                                 <!-- <td>{{ ucwords(str_replace('_',' ',$trans->payment_mode))}}</td> -->
                                 <!-- <td>{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</td> -->
                                 <td><span data-toggle="tooltip" data-placement="bottom" title="<?= (!empty($payment->bank_name)) ? $payment->bank_name : '' ?>">{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</span></td>
                                 <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
                                     <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
                                 </td>
                                 <!-- <td><?= (!empty($payment->bank_name)) ? $payment->bank_name : '-' ?></td> -->
                                 <td>{{ !empty($trans->response['utr_number'])?$trans->response['utr_number']:'-' }}</td>
                                 <td>{!! $status !!}</td>
                                 <td>{{ date('d M y H:i',$trans->created) }}</td>

                             </tr>
                             @endforeach
                         </tbody>

                     </table>
                     {{ $transactions->appends($_GET)->links()}}
                 </div>
                 <!-- /.card-body -->

             </div>

             @include('retailer.transaction.dmt')
             @include('retailer.transaction.payout')
         </div>
     </div>