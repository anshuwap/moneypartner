 <!-- Topup Request List -->
 <div class="card direct-chat direct-chat-primary">
   <div class="card-header">
     <h3 class="card-title">Topup Request</h3>

   </div>
   <!-- /.card-header -->
   <div class="card-body">
     <!-- Conversations are loaded here -->
     <table class="table table-hover text-nowrap table-sm">

       <tr>
         <th>Sr No.</th>
         <th>Transaction Id</th>
         <th>Payment Channel</th>
         <th>Retailer Name</th>
         <th>Amount</th>
         <th>Payment Mode</th>
         <th>Payment Date</th>
         <th>Status</th>
         <th>Action</th>
       </tr>

       @php
       $i =0;
       @endphp
       @if(!empty($topup_request))

       @foreach($topup_request as $key=>$topup)
       <?php
        if ($topup->status == 'success') {
          $status = 'Approved';
        } else if ($topup->status == 'rejected') {
          $status = 'Rejected';
        } else {
          $status = 'Pending';
        }

        ?>
       <tr>
         <td>{{ ++$i }}</td>
         <td>{{ !empty($topup->payment_id)?$topup->payment_id:'' }}</td>
         <td>{{ (!empty($topup->payment_channel))?ucwords($topup->payment_channel):'-' }}</td>
         <td><a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="{{ $topup->comment }}">{{ !empty($topup->RetailerName['full_name']) ? $topup->RetailerName['full_name'] : '' }}</a></td>
         <td>{!! mSign($topup->amount) !!}</td>
         <td>{{ ucwords(str_replace('_', " ", $topup->payment_mode)) }}</td>
         <td>{{ date('d M Y h:i:s A', $topup->payment_date) }}</td>
         <td id="status-{{ $topup->id }}">
           {{ $status }}
         </td>
         <td>
           <div id="action-{{$topup->id}}">
             <a href="javascript:void(0);" class="text-success view-topup-request" topup_id="{{ $topup->id }}" data-toggle="tooltip" data-placement="bottom" title="View Details"><i class="fas fa-eye"></i></i></a>&nbsp;
             <a href="javascript:void(0);" class="text-ingfo add-topup-request" topup_id="{{ $topup->id }}" data-toggle="tooltip" data-placement="bottom" title="Approve Topup"><i class="fas fa-plus-circle"></i></a>
           </div>
         </td>
       </tr>
       @endforeach
       @else
       <tr>
         <td colspan="7" style="text-align:center;">There is no any Topup Request</td>
       </tr>

       @endif

     </table>
   </div>
 </div>

 @push('custom-script')

 <!-- Modal -->
 <div class="modal fade" id="addTopup-request" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
     <div class="modal-content">
       <div class="modal-header">
         <h5 class="modal-title" id="exampleModalLongTitle">Place A Comment</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
       <div class="modal-body">

         <form action="{{ url('distributor/topup-request') }}" id="topup-request" method="post">

           <input type="hidden" name="id" id="topup_id" value="">
           <div class="form-group">
             <select name="status" class="form-control form-control-sm" id="status" required='required'>
               <option value="">Select</option>
               <option value="success">Approved</option>
               <option value="rejected">Rejected</option>
             </select>
           </div>

           <div id="topup-channel"></div>
           <div class="form-group">
             <textarea name="comment" rqueired="required" id="comment-" class="form-control" rows="5" placeholder="Enter Comment Here"></textarea>
           </div>
           <div class="form-group">
             <input type="submit" class="btn-sm btn btn-success" id="submit_btn" value="Submit">
             <a href="javascript:void(0)" class=" btn btn-sm btn-danger" data-dismiss="modal" aria-label="Close">
               Cancel
             </a>
           </div>
         </form>

       </div>
     </div>
   </div>
 </div>


 <!-- Modal -->
 <div class="modal fade" id="topup-request-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
     <div class="modal-content">
       <div class="modal-header">
         <h5 class="modal-title" id="exampleModalLongTitle">Topup Request Details</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
       <div class="modal-body">
         <div id="dataVal">

         </div>
         <div id="topup-form">
           <!-- <a href="javascript:void(0)" class="btn-sm btn-info" id="action1">Action</a> -->
           <div class="row" id="placeComment" style="display: none;">
             <div class="col-md-12 border mt-2">
               <form action="{{ url('distributor/topup-request') }}" id="topup-request" method="post">
                 <div class="tooltip-title">
                   <h6>Place a Comment</h6>
                 </div>
                 <input type="hidden" name="id" id="topup_id" value="">
                 <div class="form-group">
                   <select name="status" class="form-control control-sm" required='required'>
                     <option value="">Select</option>
                     <option value="success">Approved</option>
                     <option value="rejected">Rejected</option>
                   </select>
                 </div>
                 <div class="form-group">
                   <textarea name="comment" rqueired="required" id="comment-" class="form-control" rows="5" placeholder="Enter Comment Here"></textarea>
                 </div>
                 <div class="form-group">
                   <input type="submit" class="btn-sm btn btn-success" value="Submit">
                 </div>
               </form>
             </div>
           </div>

         </div>
       </div>
     </div>
   </div>
 </div>

 <script>
   $(document).ready(function() {

     $('#status').change(function() {

       var status = $(this).val();
       if (status == 'success') {
         $('#topup-channel').html(`<div class="form-group">
                   <select name="payment_channel" class="form-control form-control-sm" id="payment_channel">
                     <option value="">Select</option>
                     <?php foreach ($payment_channel as $channel) {
                        echo '<option value="' . $channel->name . '">' . $channel->name . '</option>';
                      } ?>
                   </select>
                   <span id="payment_channel_msg" class="custom-text-danger"></span>
                 </div>`);
       } else {
         $('#topup-channel').html(``);
       }
     })

     /*start form submit functionality*/
     $("form#topup-request").submit(function(e) {
       e.preventDefault();
       var id = $('#topup_id').val();
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
           $('.has-loader').addClass('has-loader-active');
           $('#submit_btn').val('Submitting...');
         },
         success: function(res) {
           //hide loader
           $('.has-loader').removeClass('has-loader-active');
           $('#submit_btn').val('Submit');


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
             $('#status-' + res.id).html(res.status_msg);
             $('form#topup-request')[0].reset();
             $('#action-' + id).remove();
             setTimeout(function() {
               location.reload();
             }, 1000)
           }
         }
       });
     });
     /*end form submit functionality*/


     $('.view-topup-request').click(function() {
       var topup_id = $(this).attr('topup_id');
       $('#topup_id').val(topup_id);
       $.ajax({
         url: "{{ url('distributor/topup-request-details') }}/" + topup_id,
         type: 'GET',
         dataType: 'JSON',
         success: function(res) {
           $('#dataVal').html(res.data);
           $('#topup-form').show();
           if (res.show_action)
             $('#topup-form').hide();
           $('#topup-request-details').modal('show');
         }
       })
     })

     $('.add-topup-request').click(function() {
       var topup_id = $(this).attr('topup_id');
       $('#topup_id').val(topup_id);
       $('#addTopup-request').modal('show');
     })

   });
 </script>

 @endpush