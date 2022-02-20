  <table id="table" class="table table-hover text-nowrap table-sm">
              <thead>
                <tr>
                  <th>Sr. No.</th>
                  <th>Amount</th>
                  <th>Beneficiary Name</th>
                  <th>IFSC</th>
                  <th>Account No./UPI Id</th>
                  <th>Bank Name</th>
                  <th>Status</th>
                  <th>Datetime</th>
                </tr>
              </thead>

              @if(!empty($offlinePayouts))
              <tbody>
                @foreach($offlinePayouts as $key=>$trans)
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
                  <td>{!! mSign($trans->amount) !!}</td>
                  <td>{{ ucwords($trans->receiver_name)}}</td>
                  <td>{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</td>
                  <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
                    <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
                  </td>
                  <td><?= (!empty($payment->bank_name)) ? $payment->bank_name : '-' ?></td>
                  <td>{!! $status !!}</td>
                  <td>{{ date('d,M y H:i A',$trans->created) }}</td>

                </tr>
                @endforeach
                @else
                <tr>
                  <td colspan="7" style="text-align:center;">There is no any Topup Request</td>
                </tr>
                @endif
              </tbody>
            </table>