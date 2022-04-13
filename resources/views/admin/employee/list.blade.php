@extends('admin.layouts.app')

@section('content')

<div class="row">
    <div class="col-12 mt-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Employee List</h3>
                <div class="card-tools">
                    <a href="{{ url('admin/employee/create') }}" class="btn btn-sm btn-success mr-4" id="create_employee"><i class="fas fa-plus-circle"></i>&nbsp;Add</a>
                </div>
            </div>

            <!-- /.card-header -->
            <div class="card-body table-responsive ">
                <table id="table" class="table table-hover table-sm text-nowrap">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Mobile Number</th>
                            <th>Gender</th>
                            <th>Address</th>
                            <th>Created Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $key=>$employee)
                        <?php if ($employee->status == 1) {
                            $status = ' <a href="javascript:void(0);"><span class="badge badge-success activeVer" id="active_' . $employee->_id . '" _id="' . $employee->_id . '" val="0">Active</span></a>';
                        } else {
                            $status = ' <a href="javascript:void(0)"><span class="badge badge-danger activeVer" id="active_' . $employee->_id . '" _id="' . $employee->_id . '" val="1">Inactive</span></a>';
                        } ?>
                        <tr>
                            <td>{{ ++$key}}</td>
                            <td>{{ $employee->full_name }}</td>
                            <td>{{ $employee->email}}</td>
                            <td>{{ $employee->mobile_number}}</td>
                            <td>{{ ucwords($employee->gender) }}</td>
                            <td>{{ $employee->address}}</td>
                            <td>{{ date('d,M Y',strtotime($employee->created_at))}}</td>
                            <td>{!! $status !!}</td>
                            <td><a href="{{ url('admin/employee/' . $employee->_id . '/edit')}}" class="text-info" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="far fa-edit"></i></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $employees->appends(request()->toArray())->links() }}
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

        $(document).on('click', '.remove_upi_id', function() {
            var id = $(this).attr('upi_id_id');
            var url = "{{ url('admin/upi') }}/" + id;
            var tr = $(this).parent().parent();
            removeRecord(tr, url);
        })


        $(document).on('click', '.activeVer', function() {
            var id = $(this).attr('_id');
            var val = $(this).attr('val');
            $.ajax({
                'url': "{{ url('admin/employee-status') }}",
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