@extends('admin.layouts.app')

@section('content')
@section('page_heading', 'Outlet List')

<div class="row">
  <div class="col-12 mt-2">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Outlet List</h3>
        <div class="card-tools">
          <a href="{{ url('admin/outlets/create') }}" class="btn btn-sm btn-success mr-4"><i class="fas fa-plus-circle"></i>&nbsp;Add</a>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body table-responsive py-4">
        <table id="table" class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th>Sl No.</th>
              <th>Outlet No./Code</th>
              <th>Name</th>
              <th>Mobile No.</th>
              <th>Outlet Name</th>
              <th>State/City</th>
              <th>Available Balance</th>
              <th>Created Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>

        </table>
      </div>
      <!-- /.card-body -->

    </div>
    <!-- /.card -->
  </div>
</div>
<!-- /.row -->

@push('custom-script')

<script type="text/javascript">
  $(document).ready(function() {
    // function deleteRecord(id) {
    //   // var id = $(this).attr('_id');
    //   var parent = $(this).parent().parent();
    //   swal({
    //       title: "Are you sure?",
    //       text: "Once deleted, you will not be able to recover this record!",
    //       icon: "warning",
    //       buttons: true,
    //       dangerMode: true,
    //     })
    //     .then((willDelete) => {
    //       $.ajax({
    //         type: "POST",
    //         url: "" + id,
    //         data: {
    //           'id': id
    //         },
    //         dataType: 'json',
    //         success: function(res) {
    //           if (res.status == 'success') {
    //             location.reload();
    //           }
    //         }
    //       });
    //       if (willDelete) {
    //         var iconClass = 'danger';
    //         if (res.status == 'success')
    //           var iconClass = 'success';
    //         swal(`${res.msg}`, {
    //           icon: iconClass,
    //         });
    //       } else {
    //         swal("Your record is safe!");
    //       }
    //     });
    // }

    $('#table').DataTable({
      lengthMenu: [
        [10, 30, 50, 100, 500],
        [10, 30, 50, 100, 500]
      ], // page length options

      bProcessing: true,
      serverSide: true,
      scrollY: "auto",
      scrollCollapse: true,
      'ajax': {
        "dataType": "json",
        url: "{{ url('admin/outlets-ajax') }}",
        data: {}
      },
      columns: [{
          data: "sl_no"
        },
        {
          data: 'outlet_no'
        },
        {
          data: "name"
        },
        {
          data: 'mobile_no'
        },
        {
          data: 'outlet_name'
        },
        {
          data: "state"
        },
        {
          data: "available_blance"
        },
        {
          data: "created_date"
        },
        {
          data: "status"
        },
        {
          data: "action"
        }
      ],

      columnDefs: [{
        orderable: false,
        targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
      }],
    });

    $(document).on('click', '.activeVer', function() {
      var id = $(this).attr('_id');
      var val = $(this).attr('val');
      $.ajax({
        'url': "{{ url('admin/outlets-status') }}",
        data: {
          "_token": "{{ csrf_token() }}",
          'id': id,
          'status': val
        },
        type: 'POST',
        dataType: 'json',
        success: function(res) {
          if (res.val == 1) {
            $('#active_' + id).text('Active');
            $('#active_' + id).attr('val', '0');
            $('#active_' + id).removeClass('badge-danger');
            $('#active_' + id).addClass('badge-success');
          } else {
            $('#active_' + id).text('Inactive');
            $('#active_' + id).attr('val', '1');
            $('#active_' + id).removeClass('badge-success');
            $('#active_' + id).addClass('badge-danger');
          }
          Swal.fire(
            `${res.status}!`,
            res.msg,
            `${res.status}`,
          )
        }
      })

    })

  });
</script>
@endpush
@endsection