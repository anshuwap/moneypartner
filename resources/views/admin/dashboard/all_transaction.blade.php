  <div class="card direct-chat direct-chat-primary">
      <div class="card-header">

          <div class="row">
              <div class="col-md-10">
                  <h3 class="card-title">Transaction Request</h3>
              </div>
              <!-- <div class="col-md-2 float-right">
                  <select class="form-control-sm form-control" name="type" id="mode">
                      <option value=" ">All</option>
                      <option value="bank_account" {{ ( !empty($mode) && $mode == 'bank_account')?'selected':''}}>Bank Account</option>
                      <option value="upi" {{ ( !empty($mode) && $mode == 'upi')?'selected':''}}>UPI</option>
                  </select>

              </div> -->
          </div>
      </div>

      <!-- /.card-header -->
      <div class="card-body pl-2 pr-2" style="height: 400px;">
          <!-- Conversations are loaded here -->

          <table id="table" class="table table-hover table-sm">

              <tr>
                  <th>Sr. No.</th>
                  <th>Customer</th>
                  <th>Transaction Id</th>
                  <th>Mode</th>
                  <th>Amount</th>
                  <th>Beneficiary </th>
                  <th>IFSC</th>
                  <th>Account No.</th>
                  <th>Bank Name</th>
                  <th>Status</th>
                  <th>Datetime</th>
                  <th>Action</th>
              </tr>


              @foreach($transaction as $key=>$trans)
              <?php

                $payment = (object)$trans->payment_channel;

                if ($trans->status == 'success') {
                    $status = '<span class="tag-small">' . ucwords($trans->status) . '</span>';
                    $action = '-';
                } else if ($trans->status == 'rejected') {
                    $status = '<span class="tag-small-danger">' . ucwords($trans->status) . '</span>';
                    $action = '-';
                } else {

                    $status = '<span class="tag-small-warning">' . ucwords($trans->status) . '</span>';
                    $action = '<a href="javascript:void(0);" payment_mode="' . $trans->payment_mode . '" class="btn btn-danger btn-sm retailer_trans" _id="' . $trans->_id . '">Action</a>';
                } ?>
              <tr>
                  <td>{{ ++$key }}</td>
                   <td><div style="display: grid;"><span>{{ ucwords($trans->sender_name)}}</span><span style="font-size: 13px;">{{ $trans->mobile_number }}</span></div></td>
                  <td>{{ $trans->transaction_id }}</td>
                  <td><span class="tag-small">{{ ucwords(str_replace('_',' ',$trans->type)) }}</span></td>
                  <td>{!! mSign($trans->amount) !!}</td>
                  <td>{{ ucwords($trans->receiver_name)}}</td>
                  <td>{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</td>
                  <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
                      <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
                  </td>
                  <td><?= (!empty($payment->bank_name)) ? $payment->bank_name : '-' ?></td>
                  <td>{!! $status !!}</td>
                  <td>{{ date('d,M y H:i A',$trans->created) }}</td>
                  <td>
                      {!! $action !!}</td>
              </tr>
              @endforeach

          </table>
      </div>
  </div>

  <!--start retailer transfer module-->
  @push('modal')

  <!-- Modal -->
  <div class="modal fade" id="approve_modal_dashboard" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="heading_bank_dashboard">success/Reject Request</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>

              <div class="cover-loader-modal d-none">
                  <div class="loader-modal"></div>
              </div>

              <div class="modal-body">
                  <form id="approve_trans_dashboard" action="{{ url('admin/a-transaction') }}" method="post">
                      @csrf
                      <div class="row">
                          <div class="col-md-12">
                              <input type="hidden" id="trans_id_dahboard" name="trans_id">
                              <input type="hidden" id="key_dashboard" name="key">

                              <div class="form-group" id="type-m">
                                  <label>Select</label>
                                  <select name="type" class="form-control form-control-sm" id="type">
                                      <option value="">Select</option>
                                      <option value="api">Api</option>
                                      <option value="manual">Manual</option>
                                  </select>
                              </div>

                              <div id="action">
                              </div>

                              <div id="success_dashboard"></div>

                              <div class="form-group" id="comment-field_dashboard" style="display: none;">
                                  <label>Comment</label>
                                  <select name="response[msg]" class="form-control form-control-sm" id="comment_dashboard">

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

  <script>
      $(document).on('click', '.retailer_trans', function(e) {
          e.preventDefault();
          $('#trans_id_dahboard').val($(this).attr('_id'));
          $('#key_dashboard').val(key_dashboard);

          var payment_mode = $(this).attr('payment_mode');
          if (payment_mode == 'upi') {
              $('#type-m').hide();
              $('#success_dashboard').html(``);
              upi();
          } else {
              $('#type-m').show();
              $('#success_dashboard').html(``);
              $('#action').html(``);
          }
          $('#approve_modal_dashboard').modal('show');
      })


      function upi() {
          $('#action').html(` <div class="form-group">
                                   <label>Action</label>
                                   <select name="status" id="status-select-dashboard" class="form-control form-control-sm">
                                       <option value="">Select</option>
                                       <option value="success">success</option>
                                       <option value="pending">Pending</option>
                                       <option value="rejected">Rejected</option>
                                   </select>
                                   <span id="status_msg" class="custom-text-danger"></span>
                               </div>

                                <div class="form-group" id="challel">
                                   <label>Select Payment Channel</label>
                                   <select name="response[payment_mode]" class="form-control form-control-sm" id="payment_channel">
                                       <option value="">Select</option>
                                       <?php foreach ($payment_channel as $channel) {
                                            echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                                        } ?>
                                   </select>
                                   <span id="payment_channel_msg" class="custom-text-danger"></span>
                               </div>`);
      }


      $('#type').change(() => {
          let status = $('#type').val();
          if (status == 'manual') {
              $('#approve_trans_dashboard').attr('action', '{{url("admin/a-transaction")}}');
              $('#action').html(` <div class="form-group">
                                   <label>Action</label>
                                   <select name="status" id="status-select-dashboard" class="form-control form-control-sm">
                                       <option value="">Select</option>
                                       <option value="success">success</option>
                                       <option value="pending">Pending</option>
                                       <option value="rejected">Rejected</option>
                                   </select>
                                   <span id="status_msg" class="custom-text-danger"></span>
                               </div>`);
          } else if (status == 'api') {
              $('#approve_trans_dashboard').attr('action', '{{url("admin/a-store-api")}}');
              $('#action').html(`<div class="form-group">
               <select class="form-control form-control-sm" name="api" id="api" required>
               <option value=''>Select</option>
               <option value="payunie_preet_kumar">Payunie - PREET KUMAR</option>
               <option value="payunie_parveen">Payunie - PRAVEEN</option>
               </select>
               </div>`);
          }
      })


      $(document).on('change', '#status-select-dashboard', function() {
          let status = $('#status-select-dashboard').val();

          if (status == 'success') {
            $('#challel').html(``);
              $('#success_dashboard').html(`<div class="form-group">
                   <label>Select Payment Channel</label>
                   <select name="response[payment_mode]" class="form-control form-control-sm" id="payment_channel">
                     <option value="">Select</option>
                     <?php foreach ($payment_channel as $channel) {
                            echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                        } ?>
                   </select>
                   <span id="payment_channel_msg" class="custom-text-danger"></span>
                 </div>
                 <div class="form-group">
                                <label>UTR/Transaction</label>
                                <input type="text" placeholder="UTR/Transaction" id="utr" name="response[utr_number]" class="form-control form-control-sm">
                                <span id="utr_transaction_msg" class="custom-text-danger"></span>
                            </div>`);
          } else if (status == 'rejected') {
            $('#challel').html(``);
              $('#success_dashboard').html(``);
          } else {
            $('#challel').html(``);
              $('#success_dashboard').html(`<div class="form-group">
                   <label>Select Payment Channel</label>
                   <select name="response[payment_mode]" class="form-control form-control-sm" id="payment_channel">
                     <option value="">Select</option>
                     <?php foreach ($payment_channel as $channel) {
                            echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                        } ?>
                   </select>
                   <span id="payment_channel_msg" class="custom-text-danger"></span>
                 </div>`);
          }
      })


      $(document).on('change', '#status-select-dashboard', function(e) {
          e.preventDefault();

          var type = $(this).val();

          if (type == '') {
              $('#comment-field_dashboard').hide();
          } else {
              $.ajax({
                  url: "<?= url('admin/a-trans-comment') ?>",
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

      $('#mode').change(function() {

          var mode = $(this).val();
          location.href = '{{ url("admin/dashboard") }}?mode=' + mode;
      })

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