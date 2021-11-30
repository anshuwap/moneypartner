$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
function removeRecord(tr, url) {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: 'btn btn-success',
      cancelButton: 'btn btn-danger'
    },
    buttonsStyling: false
  })

  swalWithBootstrapButtons.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'No, cancel!',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: url,
        type: 'POST',
        data: { _method: 'delete' },

        success: function (res) {

          if (res.status == 'success') {
            swalWithBootstrapButtons.fire(
              'Removed!',
              'Your Record has been Removed.',
              'success'
            )

          tr.fadeOut('slow',()=>{
            $(tr).remove();
          })
          } else if (res.status == 'error') {
            swalWithBootstrapButtons.fire(
              'Error!',
              'Your Record has not Removed.',
              'error'
            )
          }
        }
      });

    } else if (
      /* Read more about handling dismissals below */
      result.dismiss === Swal.DismissReason.cancel
    ) {
      swalWithBootstrapButtons.fire(
        'Cancelled',
        'Your imaginary Record is safe :)',
        'error'
      )
    }
  })
}