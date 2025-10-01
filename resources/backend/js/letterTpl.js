toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": false,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
  }
function getTemplateView(actionUrl = '', formData=null) {
      if (actionUrl == '') {
            return false;
      }
      alert()
     /*  $.ajax({
            type: "GET",
            url: actionUrl,
            data: formData,
            datatype: "json",
            beforeSend: function () {
                  $('.uc-loader').show();
            },
            success: function (response) {
                  $('.uc-loader').hide();
                  if (response.status == true) {
                        $('.reportrange span').html('');
                        $('.date_filter_range').val('');
                        $('.mybids-daterange').hide();
                        myBidList()
                        toastr.success(response.message);
                  } else {
                        toastr.error(response.message);
                  }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                  var msg = '';
                  // if (jqXHR.status === 0) {
                  //     msg = 'Not connect.\n Verify Network.';
                  // } else if (jqXHR.status == 404) {
                  //     msg = 'Requested page not found. [404]';
                  // } else if (jqXHR.status == 500) {
                  //     msg = 'Internal Server Error [500].';
                  // } else if (exception === 'parsererror') {
                  //     msg = 'Requested JSON parse failed.';
                  // } else if (exception === 'timeout') {
                  //     msg = 'Time out error.';
                  // } else if (exception === 'abort') {
                  //     msg = 'Ajax request aborted.';
                  // } else {
                  //     msg = 'Uncaught Error.\n' + jqXHR.responseJSON.message
                  // } 
                  toastr.error(jqXHR.responseJSON.message);
                  $('.uc-loader').hide();
            }
      }); */
}
(function ($) {
      // toastr.success("===========");
})(jQuery);