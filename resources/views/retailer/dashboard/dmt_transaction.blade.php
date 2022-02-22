<table id="table" class="table table-hover text-nowrap table-sm">
  <thead>
    <tr>
      <th>Sr. No.</th>
      <th>Transaction Id</th>
      <th>Amount</th>
      <th>Beneficiary Name</th>
      <th>IFSC</th>
      <th>Account No./UPI Id</th>
      <th>Bank Name</th>
      <th>Status</th>
      <th>Datetime</th>
      <th></th>
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
            <td><?= (!empty($detail['transaction_id'])) ? $detail['transaction_id'] : '' ?></td>
            <td>{!! mSign($detail['amount']) !!}</td>
            <td>{{ ucwords($detail['receiver_name'] ) }}</td>
            <td>{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</td>
            <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
              <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
            </td>
            <td><?= (!empty($payment->bank_name)) ? $payment->bank_name : '-' ?></td>
            <td>{!! $status !!}</td>
            <td>{{ date('d,M y H:i A',$detail['created'])}}</td>
            <td><a tabindex="0" class="text-success" role="button" data-toggle="popover" data-trigger="focus" title="Customer Details" data-content="{{ $trans->customer_name}},{{ $trans->mobile_number}}"><i class="fas fa-angle-down"></i></a>
            </td>
          </tr>
    <?php $i++;
        }
      }
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