@extends('admin.layouts.app')

@section('content')

<div class="cover-loader d-none">
    <div class="loader"></div>
</div>


<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h5 class="m-0">Setting</h5>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Setting</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Handle Maintanance </h3>
                    </div>
                    <div class="card-body ">
                        <form id="form" action="{{url('admin/setting')}}" method="post">
                            @csrf
                            <div class="form-group">
                                <label>Maintanace Status</label>
                                <select class="form-control form-control-sm" required name="status">
                                    <option value="">Select</option>
                                    <option value="1" {{ ($setting->status ==1)?'selected':''}}>Enable</option>
                                    <option value="0" {{ ($setting->status ==0)?'selected':''}}>Disable</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Comment</label>
                                <textarea class="form-control" name="comment" placeholder="Enter Comment" required rows="5">{{ $setting->comment }}</textarea>
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Submit" class="btn btn-sm btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
<!-- /.content -->

@push('custom-script')
<script>
    /*start form submit functionality*/
    $("form#form").submit(function(e) {
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
                $('.cover-loader').removeClass('d-none');
                $('#outlet').hide();
            },
            success: function(res) {
                //hide loader
                $('.cover-loader').addClass('d-none');
                $('#outlet').show();

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
                    $('form#form')[0].reset();
                    $('#custom-file-label').html('');
                    setTimeout(function() {
                        location.reload();
                    }, 1000)
                }
            }
        });
    });

    /*end form submit functionality*/
</script>
@endpush

@endsection