 <div class="card direct-chat direct-chat-primary">
      <div class="card-header">
        <h3 class="card-title">Topup Request</h3>

      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <!-- Conversations are loaded here -->
        <div class="direct-chat-messages">
          <table class="table table-hover text-nowrap table-sm">

            <thead>
              <tr>
                <th>Sr No.</th>
                <th>Transaction Id</th>
                <th>Retailer Name</th>
                <th>Amount</th>
                <th>Payment Mode</th>
                <th>Payment Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>

            <tbody>
              @php
              $i =0;
              @endphp
              @if(!empty($topup_request))

              @foreach($topup_request as $key=>$topup)
              <tr>
                <td>{{ ++$i }}</td>
                 <td><?= (!empty($topup->payment_id)) ? $topup->payment_id : '' ?></td>
                <td><a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="{{ $topup->comment }}">{{ $topup->retailer_name }}</a></td>
                <td>{!! mSign($topup->amount) !!}</td>
                <td>{{ $topup->payment_mode }}</td>
                <td>{{ $topup->payment_date }}</td>
                <td id="status-{{ $topup->id }}">
                  {{ $topup->status }}
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
            <tbody>
              <tr>
                <td colspan="7" style="text-align:center;">There is no any Topup Request</td>
              </tr>
            </tbody>
            @endif
            </tbody>

          </table>
        </div>
      </div>
    </div>