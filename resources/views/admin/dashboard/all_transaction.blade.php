  <div class="card direct-chat direct-chat-primary">
      <div class="card-header">

          <div class="row">
              <div class="col-md-10">
                  <h3 class="card-title">Transaction Request</h3>
              </div>
              <div class="col-md-2 d-flex">
                  <div>
                      <a href="javascript:void(0);" id="import" class="btn btn-sm btn-success"><i class="fas fa-cloud-upload-alt"></i>&nbsp;Bulk Upload</a>
                  </div>
                  <div id="bluckAssignBlock" style="pointer-events:none !important;" class="ml-1">
                      <button class="btn btn-sm btn-success" aria-haspopup="true" id="bluckAssignBtn" disabled>
                          <i class="fas fa-radiation-alt"></i>&nbsp; Action
                      </button>
                  </div>
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
      <div class="card-body pl-2 pr-2">
          <!-- Conversations are loaded here -->

          <table id="table" class="table table-hover table-sm">

              <tr>
                  <th><input type="checkbox" class="select_all" id="checkAll" /></th>
                  <th style="width:55px;">Sr. No.</th>
                  <th style="width: 110px;">Outlet</th>
                  <!-- <th style="width: 115px;"> Transaction Id</th> -->
                  <th style="width:100px;">Mode</th>
                  <th>Amount</th>
                  <th>Beneficiary </th>
                  <th>IFSC</th>
                  <th>Account No.</th>
                  <!-- <th>Bank Name</th> -->
                  <th>Status</th>
                  <th>Datetime</th>
                  <th>Action</th>
              </tr>

              @foreach($transaction as $key=>$trans)
              <?php

                $payment = (object)$trans->payment_channel;
                $comment = !empty($trans->response['msg']) ? $trans->response['msg'] : '';

                if ($trans->status == 'success') {
                    $status = '<span class="tag-small"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                    $action = '-';
                } else if ($trans->status == 'progress') {
                    $status = '<span class="tag-small-purple"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                    $action = '-';
                } else if ($trans->status == 'rejected') {
                    $status = '<span class="tag-small-danger"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                    $action = '-';
                } else {

                    $status = '<span class="tag-small-warning"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom" title="' . $comment . '">' . ucwords($trans->status) . '</a></span>';
                    $action = '<a href="javascript:void(0);" payment_mode="' . $trans->payment_mode . '" class="btn btn-danger btn-xs retailer_trans" _id="' . $trans->_id . '"><i class="fas fa-radiation-alt"></i>&nbsp;Action</a>';
                } ?>
              <tr>
                  <td>
                      <input type="checkbox" class="select_me checkbox" value="{{ $trans->_id }}" />
                  </td>
                  <td>{{ ++$key }}</td>
                  <td>
                      <span data-toggle="tooltip" data-placement="bottom" title="{{ $trans->transaction_id }}"> {{ (!empty($trans->OutletName['outlet_name']))?$trans->OutletName['outlet_name']:'-';}}</span>
                  </td>
                  <!-- <td><span data-toggle="tooltip" data-placement="bottom" title="{{ ucwords($trans->sender_name)}},{{$trans->mobile_number}}">{{ $trans->transaction_id }}</span></td> -->
                  <td><span class="tag-small">{{ ucwords(str_replace('_',' ',$trans->type)) }}</span></td>
                  <td style="width: 90px;">{!! mSign($trans->amount) !!}</td>
                  <td>{{ ucwords($trans->receiver_name)}}</td>
                  <td><span data-toggle="tooltip" data-placement="bottom" title="<?= (!empty($payment->bank_name)) ? $payment->bank_name : '' ?>">{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</span></td>
                  <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
                      <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
                  </td>
                  <!-- <td><?= (!empty($payment->bank_name)) ? $payment->bank_name : '-' ?></td> -->
                  <td>{!! $status !!}</td>
                  <td>{{ date('d M y H:i',$trans->created) }}</td>
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
  <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" id="preview-modal" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">Import Csv File</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>

              <!-- for loader -->
              <div class="cover-loader-modal d-none">
                  <div class="loader-modal"></div>
              </div>

              <div class="modal-body pl-2 pr-2">
                  <div id="import-file">
                      <p>Download sample Payout Transaction Import(CSV) file : <a href="{{ url('admin/export') }}" class="text-green">Download</a></p>
                      <form id="import" action="{{ url('admin/import') }}" method="post" enctype="multipart/form-data">
                          @csrf

                          <div class="form-row">
                              <div class="form-group col-md-10">
                                  <div class="input-group">
                                      <div class="custom-file">
                                          <input type="file" name="file" class="custom-file-input custom-file-input-sm" id="imgInp" accept=".csv">
                                          <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                      </div>
                                  </div>
                                  <span id="file_msg" class="custom-text-danger"></span>
                              </div>

                              <div class="form-group col-md-2">
                                  <input type="submit" class="btn btn-success btn-sm" id="submit_bank_charges" value="Import">
                              </div>

                          </div>
                      </form>
                  </div>

              </div>
          </div>
      </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="approve_modal_dashboard" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="heading_bank_dashboard">Success/Reject Request</h5>
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
                                  <select name="type" class="form-control form-control-sm" id="type" required>
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
      $('#import').click(function(e) {
          e.preventDefault();
          $('form#import')[0].reset();
          let url = '{{ url("admin/import") }}';
          $('form#import').attr('action', url);
          $('#importModal').modal('show');
      })

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
              $('#comment-field_dashboard').hide();
          }
          $('#approve_modal_dashboard').modal('show');
      })

      $('#type').change(() => {
          let status = $('#type').val();
          if (status == 'manual') {
              $('#approve_trans_dashboard').attr('action', '{{url("admin/a-transaction")}}');
              $('#action').html(` <div class="form-group">
                                   <label>Action</label>
                                   <select name="status" id="status-select-dashboard" class="status-select-dashboard form-control form-control-sm" required>
                                       <option value="">Select</option>
                                       <option value="success">Success</option>
                                       <option value="pending">Pending</option>
                                       <option value="rejected">Rejected</option>
                                   </select>
                                   <span id="status_msg" class="custom-text-danger"></span>
                               </div>`);
          } else if (status == 'api') {
              $('#comment-field_dashboard').hide();
              $('#challel').html(``);
              $('#success_dashboard').html(``);
              $('#approve_trans_dashboard').attr('action', '{{url("admin/a-store-api")}}');
              $('#action').html(`<div class="form-group">
               <select class="form-control form-control-sm" name="api" id="api" required>
               <option value=''>Select</option>
               <option value="payunie_preet_kumar">Payunie - PREET KUMAR</option>
               <option value="payunie_rashid_ali">Payunie -Rashid Ali</option>
               <option value="pay2all">Pay2ALL - PRAVEEN</option>
                <option value="odnimo">Odnimo</option>
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
                   <select name="response[payment_mode]" class="form-control form-control-sm" id="payment_channel" required>
                     <option value="">Select</option>
                     <?php foreach ($payment_channel as $channel) {
                            echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                        } ?>
                   </select>
                   <span id="payment_channel_msg" class="custom-text-danger"></span>
                 </div>
                 <div class="form-group">
                                <label>UTR/Transaction</label>
                                <input type="text" placeholder="UTR/Transaction" required id="utr" name="response[utr_number]" class="form-control form-control-sm">
                                <span id="utr_transaction_msg" class="custom-text-danger"></span>
                            </div>`);
          } else if (status == 'rejected') {

          } else {
              $('#challel').html(``);
              $('#success_dashboard').html(`<div class="form-group">
                   <label>Select Payment Channel</label>
                   <select name="response[payment_mode]" class="form-control form-control-sm" id="payment_channel" required>
                     <option value="">Select</option>
                     <?php foreach ($payment_channel as $channel) {
                            echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                        } ?>
                   </select>
                   <span id="payment_channel_msg" class="custom-text-danger"></span>
                 </div>`);
          }
      })


      $(document).on('change', '.status-select-dashboard', function(e) {
          e.preventDefault();

          var type = $(this).val();

          if (type == '') {
              $('#comment-field_dashboard').hide();
              $('#comment_field_dashboard1').show();
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
                      $('#comment_field_dashboard1').show();
                      $('#comment_dashboard').html(res);
                      $('#comment_dashboard1').html(res);
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


      /*start bulk Approve transaction*/
      $('#checkAll').click(function() {

          $('.table input:checkbox').prop('checked', this.checked);
          if ($(".table input[type=checkbox]:checked").length > 1) {

              $('#bluckAssignBtn').prop('disabled', false);
              $('#bluckAssignBlock').removeAttr('style');
          } else {
              $('#bluckAssignBtn').prop('disabled', true);
              $('#bluckAssignBlock').css({
                  'pointer-events': 'none !important;'
              });
          }
      });
      $('.checkbox').click(function() {
          if ($(".table input[type=checkbox]:checked").length > 0) {
              $('#bluckAssignBtn').prop('disabled', false);
              $('#bluckAssignBlock').removeAttr('style');
          } else {
              $('#bluckAssignBtn').prop('disabled', true);
              $('#bluckAssignBlock').css({
                  'pointer-events': 'none !important;'
              });
          }
      });



      $('#bluckAssignBtn').click(function() {
          $('#approve_trans_')[0].reset();
          var transID = [];
          $(".table input[type=checkbox]:checked").each(function(i) {
              if ($(this).val() != 'on')
                  transID.push($(this).val());
          });
          $('#trans_id_dahboard1').val(transID);
          $('#bluckAssignBtn1').modal('show');
      })
      /*end bulk approve transaction*/
  </script>

  <div class="modal fade" id="bluckAssignBtn1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="heading_bank_dashboard">Success/Reject Request</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>

              <div class="cover-loader-modal d-none">
                  <div class="loader-modal"></div>
              </div>

              <div class="modal-body">
                  <form id="approve_trans_" action="{{ url('admin/bulk-action') }}" method="post">
                      @csrf
                      <div class="row">
                          <div class="col-md-12">
                              <input type="hidden" id="trans_id_dahboard1" name="trans_id">

                              <div class="form-group">
                                  <label>Select Api</label>
                                  <select class="form-control form-control-sm" name="api" id="api" required>
                                      <option value=' '>Select</option>
                                      <option value="payunie_preet_kumar">Payunie - PREET KUMAR</option>
                                      <option value="payunie_rashid_ali">Payunie -Rashid Ali</option>
                                      <option value="pay2all">Pay2ALL - PRAVEEN</option>
                                      <option value="odnimo">Odnimo</option>
                                  </select>
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
  <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg" id="preview-modal" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">Preview Transaction</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>

              <!-- for loader -->
              <div class="cover-loader-modal d-none">
                  <div class="loader-modal"></div>
              </div>

              <div class="modal-body pl-2 pr-2">
                  <div class="d-none" id="show-pin">
                      <input type="hidden" id="no_of_record">
                      <input type="hidden" id="total_amount">
                      <input type="hidden" id="api_val">
                      <div id="preview-import-data">
                      </div>

                      <div class="form-group text-center">
                          <button type="button" id="paied" class="btn btn-success btn-sm"><i class="fas fa-compress-arrows-alt"></i>&nbsp;Send</button>
                          <button type="button" class="btn btn-sm btn-success" data-dismiss="modal" aria-label="Close">
                              <i class="fas fa-times"></i>&nbsp;Close
                          </button>
                      </div>
                  </div>

              </div>
          </div>
      </div>
  </div>


  <script>
      /*start form submit functionality*/
      $("form#approve_trans_").submit(function(e) {
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

                  if (res.status == 'preview') {
                      $('#import-file').addClass('d-none');
                      $('#preview-modal').addClass('modal-lg-custom');
                      $('#preview-modal').removeClass('modal-dialog modal-dialog-centered');
                      $('#show-pin').removeClass('d-none');
                      $('#preview-import-data').html(res.data.table_data);
                      $('#no_of_record').val(res.data.no_of_record);
                      $('#total_amount').val(res.data.total_amount);
                      $('#api_val').val(res.api);
                      $('#previewModal').modal('show')
                  }

                  //for reset all field
                  if (res.status == 'success') {
                      $('form#approve_trans_')[0].reset();
                      setTimeout(function() {
                          location.reload();
                      }, 1000)
                  }
              }
          });
      });


      $('#paied').click(function(e) {
          e.preventDefault();

          var no_of_record = $('#no_of_record').val();
          var total_amount = $('#total_amount').val();
          Swal.fire({
              title: '<h6>Number Of Record&nbsp;-<b>' + no_of_record + '</b></h6><h6>Total Amount &nbsp;&nbsp;<b> &#8377;' + total_amount + '</b></h6>',
              showDenyButton: true,
              showCancelButton: false,
              confirmButtonText: 'Confirm',
              denyButtonText: `Cancel`,
          }).then((result) => {
              /* Read more about isConfirmed, isDenied below */
              if (result.isConfirmed) {
                  // Swal.fire('Saved!', '', 'success')
                  $('#hide-pin').hide();
                  $('#bluckAssignBtn1').modal('hide');
                  importSequence(0);
              } else if (result.isDenied) {
                  Swal.fire('Changes are not saved', '', 'info')
              }
          })
      })

      function importSequence(index) {
          var api = $('#api_val').val();
          var url1 = '{{ url("admin/payToApi")}}';
          $.ajax({
              data: {
                  'api': api,
                  'index': index
              },
              type: "GET",
              url: url1,
              dataType: 'json',
              success: function(res) {
                  if (index == 0)
                  $('.preview-table').remove();

                  $('#preview-table').append(res.data);

                  if (index + 1 != res.all_row) {
                      importSequence(res.index);
                  } else {
                      $('#paied').remove();
                  }
                  $('#paied').remove();
              }
          });
      }
      $('#previewModal').on('hidden.bs.modal', function() {
          location.reload();
      });
  </script>

  @endpush
  <!--end retailer transer module-->