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
               <td>{{ $i }}</td>
               <td><?= (!empty($detail['transaction_id'])) ? $detail['transaction_id'] : '' ?></td>
               <td><a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="copy" id="copy-{{$i}}" onclick="copyToClipboard('#text-{{$i}}','#copy-{{$i}}')" class="text-dark">{!! mSign($detail['amount']) !!}</a></td>
               <td>{{ ucwords($detail['receiver_name'] ) }}</td>
               <td>{{ (!empty($payment->ifsc_code))?$payment->ifsc_code:'-' }}</td>
               <td><?= (!empty($payment->account_number)) ? $payment->account_number : '' ?>
                 <?= (!empty($payment->upi_id)) ? $payment->upi_id : '' ?>
               </td>
               <td><?= (!empty($payment->bank_name)) ? $payment->bank_name : '-' ?></td>
               <td>{!! $status !!}</td>
               <td>{{ date('d,M y H:i A',$detail['created'])}}</td>
               <td>
                 <a tabindex="0" class="text-success" role="button" data-toggle="popover" data-trigger="focus" title="Customer Details" data-content="{{ $trans->customer_name}},{{ $trans->mobile_number}}"><i class="fas fa-angle-down"></i></a>

                 <a href="javascript:void(0);" class="btn btn-info btn-sm view" trans_id="{{ $trans->_id }}" _id="{{ $i }}"><i class="fas fa-eye"></i>&nbsp;view</a>
                 @if(empty($detail['admin_action']))
                 <a href="javascript:void(0);" class="btn btn-danger btn-sm customer_trans" trans_id="{{ $trans->_id }}" _id="{{ $i }}">Action</a>
                 @endif
               </td>

             </tr>
       <?php
            }
            $i++;
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


   <!--start customer module-->

   @push('modal')

   <!-- Modal -->
   <div class="modal fade" id="approve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered" role="document">
       <div class="modal-content">
         <div class="modal-header">
           <h5 class="modal-title" id="heading_bank">Approved/Reject Request</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
           </button>
         </div>

         <div class="cover-loader-modal d-none">
           <div class="loader-modal"></div>
         </div>

         <div class="modal-body">
           <form id="approve_trans" action="{{ url('admin/a-customer-trans') }}" method="post">
             @csrf
             <div class="row">
               <div class="col-md-12">
                 <input type="hidden" id="trans_id" name="trans_id">
                 <input type="hidden" id="key" name="key">

                 <div class="form-group">
                   <label>Action</label>
                   <select name="status" id="status-select" class="form-control form-control-sm">
                     <option value="">Select</option>
                     <option value="approved">Approved</option>
                     <option value="pending">Pending</option>
                     <option value="rejected">Rejected</option>
                   </select>
                   <span id="status_msg" class="custom-text-danger"></span>
                 </div>

                 <div id="approved"></div>

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

                 <div class="form-group" id="comment-field" style="display: none;">
                   <label>Comment</label>
                   <select name="comment" class="form-control form-control-sm" id="comment">

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
   <div class="modal fade" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered" role="document">
       <div class="modal-content">
         <div class="modal-header">
           <h5 class="modal-title" id="heading_bank">Account Details</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
           </button>
         </div>

         <div class="cover-loader-modal d-none">
           <div class="loader-modal"></div>
         </div>

         <div class="modal-body" id="details1">
           <div id="details"></div>
         </div>
       </div>
     </div>
   </div>

   <script>
     $(document).on('click', '.customer_trans', function(e) {
       e.preventDefault();

       $('#trans_id').val($(this).attr('trans_id'));
       $('#key').val($(this).attr('_id'));
       $('#approve_modal').modal('show');
     });

     $('#status-select').change(() => {
       let status = $('#status-select').val();
       if (status == 'approved') {
         $('#approved').html(`<div class="form-group">
                                <label>UTR/Transaction</label>
                                <input type="text" placeholder="UTR/Transaction" id="utr" name="admin_action['utr_transaction']" class="form-control form-control-sm">
                                <span id="utr_transaction_msg" class="custom-text-danger"></span>
                            </div>`);
       } else {
         $('#approved').html(``);
       }
     })


     $('#status-select').change(function(e) {
       e.preventDefault();

       var type = $(this).val();

       if (type == '') {
         $('#comment-field').hide();
       } else {
         $.ajax({
           url: "<?= url('admin/a-customer-comment') ?>",
           data: {
             'type': type
           },
           type: 'GET',
           dataType: "json",
           success: function(res) {
             $('#comment-field').show();
             $('#comment').html(res);
           }
         })
       }
     })

     /*start form submit functionality*/
     $("form#approve_trans").submit(function(e) {
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
             $('form#approve_trans')[0].reset();
             setTimeout(function() {
               location.reload();
             }, 1000)
           }
         }
       });
     });

     /*end form submit functionality*/


     //show transaction detils
     $(document).on('click', '.view', function() {
       var trnas_id = $(this).attr('trans_id');
       var key = $(this).attr('_id');

       $.ajax({
         url: "<?= url('admin/a-view-detail') ?>",
         data: {
           'id': trnas_id,
           'key': key
         },
         type: 'GET',
         dataType: "json",
         success: function(res) {

           $('#details').html(res);
           $('#view_modal').modal('show');
         }
       })
     });


     function copyToClipboard(element, copy) {
       var $temp = $("<input />");
       $("#details1").append($temp);
       $temp.val($(element).text()).select();
       document.execCommand("copy");
       $(copy).removeClass('d-none');
       $(copy).attr('title', 'Copied');
       $(copy).attr('data-original-title', 'Copied');
       $(copy).tooltip("option", "show", {
         delay: 1000
       });
       $temp.remove();
     }
   </script>

   @endpush
   <!--end customer module-->