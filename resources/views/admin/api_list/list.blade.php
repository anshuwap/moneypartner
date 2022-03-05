@extends('admin.layouts.app')

@section('content')
<style>
    INPUT[type=checkbox]:focus {
        outline: 1px solid rgba(0, 0, 0, 0.2);
    }

    INPUT[type=checkbox] {
        background-color: #DDD;
        border-radius: 2px;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 17px;
        height: 17px;
        cursor: pointer;
        position: relative;
        top: 5px;
    }

    INPUT[type=checkbox]:checked {
        background-color: #2fc296;
        background: #2fc296 url("data:image/gif;base64,R0lGODlhCwAKAIABAP////3cnSH5BAEKAAEALAAAAAALAAoAAAIUjH+AC73WHIsw0UCjglraO20PNhYAOw==") 3px 3px no-repeat;
    }
</style>
<div class="row">
    <div class="col-md-12"></div>
    <div class="col-12 mt-2 ml-auto mr-auto">
        <h5>Api List</h5>
        @foreach($apis as $key=>$api)
        <div class="card p-3">
            <form id="update-api" method="post">
                <div class="row">
                    <input type="hidden" name="id" value="{{ $api->_id }}">
                    <div class="col-md-3">
                        <div><strong>Api Name:&nbsp;&nbsp;&nbsp;</strong><span>{{ ucwords($api->name) }}</span></div>
                        <div><strong>Api URL:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong><span>{{ $api->api}}</span></div>
                    </div>
                    <div class="col-md-2">
                        <div>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Sort</span>
                                </div>
                                <input type="number" name="sort" class="form-control" value="{{ $api->sort }}" id="sort-{{ $api->_id}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <input type="checkbox" name="status" id="status" data-toggle="switchbutton" data-onlabel="Enable" data-offlabel="Disabled" data-onstyle="success" data-offstyle="danger" {{ ($api->status)?'checked':'' }}>
                    </div>
                    <div class="col-md-5">
                        @foreach($retailers as $retailer)
                        <input class="" type="checkbox" name="retailer_ids[]" value="{{ $retailer->_id }}" class="red-input" {{ (!empty($api->retailer_ids) && in_array($retailer->_id,$api->retailer_ids))?'checked':''}}>
                        <label for="" class="">{{ ucwords($retailer->full_name) }}|</label>
                        @endforeach
                    </div>
                    <div class="col-md-1">
                        <div class="float-right">
                            <button type="submit" class="btn btn-sm btn-info edit"><i class="fas fa-edit"></i>&nbsp;Update</button>
                            <!-- <a href="javascript:void(0);" class="btn btn-sm btn-info edit" api_id="{{ $api->_id}}"></a> -->
                        </div>
                    </div>

                </div>
            </form>
        </div>
        @endforeach


        <!-- /.card -->
    </div>
</div>
<!-- /.row -->

@push('modal')

<script>
    // $(document).on('click', '.edit', function(e) {
    //     e.preventDefault();
    //     var id = $(this).attr('api_id');
    //     var sort = $('#sort-' + id).val();
    //     var status = $().val();
    //     var url = "{{ url('admin/api-list-editApi') }}/";
    //     $.ajax({
    //         url: url,
    //         method: 'POST',
    //         dataType: "JSON",
    //         data: {
    //             id: id,
    //             'sort': sort,
    //         },
    //         success: function(res) {
    //             /*Start Status message*/
    //             if (res.status == 'success' || res.status == 'error') {
    //                 Swal.fire(
    //                     `${res.status}!`,
    //                     res.msg,
    //                     `${res.status}`,
    //                 )
    //             }
    //             /*End Status message*/
    //         },

    //         error: function(error) {
    //             console.log(error)
    //         }
    //     });
    // });

    /*start form submit functionality*/
    $("form#update-api").submit(function(e) {
        e.preventDefault();
        formData = new FormData(this);
        var url = "{{ url('admin/api-list-editApi') }}";
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
                $('#upi-id').hide();
            },
            success: function(res) {
                //hide loader
                $('.cover-loader-modal').addClass('d-none');
                $('#upi-id').show();


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
                    $('form#add_payment_channel')[0].reset();
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