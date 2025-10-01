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
var currentRequest = null;
function getTemplateView(actionUrl = '', formData = null) {
      if (actionUrl == '') {
            return false;
      }
      // var formId = null;
      // var outputId = null;
      currentRequest = $.ajax({
            type: "POST",
            url: actionUrl,
            data: formData,
            datatype: "json",
            cache: false,
            beforeSend: function () {
                  // var formArr = $.grep(formData, function(e){ return e.name == 'formId'});
                  // var outputIdArr = $.grep(formData, function(e){ return e.name == 'outputId'});
                  // formId = formArr[0].value;
                  // outputId = outputIdArr[0].value;
                  // console.log(outputId)
                  $('#tplTableData').html('Loading....')
                  if (currentRequest != null) {
                        // currentRequest.abort();
                  }
            },
            success: function (response) {
                  if (response.success == true) {
                        $('#'+response.data.requestData.outputId).html(response.data.view);
                        $('.templateGeneratePDF').show();
                        // toastr.success(response.message);
                  } else {
                        toastr.error(response.message);
                  }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                  var msg = '';
                  if (jqXHR.status === 0) {
                      msg = 'Not connect.\n Verify Network.';
                  } else if (jqXHR.status == 404) {
                      msg = 'Requested page not found. [404]';
                  } else if (jqXHR.status == 500) {
                      msg = 'Internal Server Error [500].';
                  } else if (exception === 'parsererror') {
                      msg = 'Requested JSON parse failed.';
                  } else if (exception === 'timeout') {
                      msg = 'Time out error.';
                  } else if (exception === 'abort') {
                      msg = 'Ajax request aborted.';
                  } else {
                      msg = 'Uncaught Error.\n' + jqXHR.responseJSON.message
                  } 
                  toastr.error(msg);
                  // toastr.error(jqXHR.responseJSON.message);
            }
      });
}

function templateGeneratePDFAjax(actionUrl = '', formData = null) {
      if (actionUrl == '') {
            return false;
      }
      // var formId = null;
      // var outputId = null;
      console.log(actionUrl)
      console.log(formData)
      // return false
      currentRequest = $.ajax({
            type: "POST",
            url: actionUrl,
            // data: $('#tplForm').serializeArray(),
            data: formData,
            // datatype: "json",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            processData: true,  
            cache: true,
            xhrFields: {
                  responseType: 'blob' // to avoid binary data being mangled on charset conversion
              },
            beforeSend: function () {
                  
            },
            success: function (blob, status, xhr) {
                  /* if (response.success == true) {
                        // $('#'+response.data.requestData.outputId).html(response.data.view);
                        toastr.success(response.message);
                  } else {
                        toastr.error(response.message);
                  } */
                         // check for a filename
        var filename = "";
        var disposition = xhr.getResponseHeader('Content-Disposition');
        if (disposition && disposition.indexOf('attachment') !== -1) {
            var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
            var matches = filenameRegex.exec(disposition);
            if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
        }

        if (typeof window.navigator.msSaveBlob !== 'undefined') {
            // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
            window.navigator.msSaveBlob(blob, filename);
        } else {
            var URL = window.URL || window.webkitURL;
            var downloadUrl = URL.createObjectURL(blob);

            if (filename) {
                // use HTML5 a[download] attribute to specify filename
                var a = document.createElement("a");
                // safari doesn't support this yet
                if (typeof a.download === 'undefined') {
                    window.location.href = downloadUrl;
                } else {
                    a.href = downloadUrl;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                }
            } else {
                window.location.href = downloadUrl;
            }

            setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 100); // cleanup
        }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                  var msg = '';
                  if (jqXHR.status === 0) {
                      msg = 'Not connect.\n Verify Network.';
                  } else if (jqXHR.status == 404) {
                      msg = 'Requested page not found. [404]';
                  } else if (jqXHR.status == 500) {
                      msg = 'Internal Server Error [500].';
                  } else if (exception === 'parsererror') {
                      msg = 'Requested JSON parse failed.';
                  } else if (exception === 'timeout') {
                      msg = 'Time out error.';
                  } else if (exception === 'abort') {
                      msg = 'Ajax request aborted.';
                  } else {
                      msg = 'Uncaught Error.\n' + jqXHR.responseJSON.message
                  } 
                  toastr.error(msg);
                  // toastr.error(jqXHR.responseJSON.message);
            }
      });

}

(function ($) {
      // toastr.success("===========");
})(jQuery);