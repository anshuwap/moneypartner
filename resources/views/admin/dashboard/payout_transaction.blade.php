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
                  $action = '<a href="javascript:void(0);" class="btn btn-danger btn-sm retailer_trans" _id="' . $trans->_id . '">Action</a>';
                } ?>
                <tr>
                  <td>{{ ++$key }}</td>
                  <td>{{ $trans->transaction_id }}</td>
                  <td>{!! mSign($trans->amount) !!}</td>
                  <td>{{ ucwords($trans->receiver_name)}}</td>
                  <td>{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</td>
                  <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
                    <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
                  </td>
                  <td><?= (!empty($payment->bank_name)) ? $payment->bank_name : '-' ?></td>
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
              <input type="hidden" id="key_dashboard" name="key">

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
    $('#key_dashboard').val(key_dashboard);
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