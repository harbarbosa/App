<script>
$(document).ready(function () {
  "use strict"; 
  $(".select2").select2();
  setDatePicker("#from_date");
  setDatePicker("#to_date");
});

var hidden_columns = [2,3,4,5], table_rec_campaign;
Dropzone.autoDiscover = false;
var expenseDropzone;
(function($) {
"use strict"; 
    table_rec_campaign = $('.table-table_pur_order');

    var Params = {
        "from_date": 'input[name="from_date"]',
        "to_date": 'input[name="to_date"]',
        "vendor": "[name='vendor[]']",
        "status": "[name='status[]']",
        "project": "[name='project[]']",
        "department": "[name='department[]']",
        "delivery_status": "[name='delivery_status[]']",
        "purchase_request": "[name='pur_request[]']"
    };

    initDataTable('.table-table_pur_order',"<?php echo get_uri('purchase/table_pur_order'); ?>", [0], [0], Params,[2, 'desc']);

    $.each(Params, function(i, obj) {
        $('select' + obj).on('change', function() {  
            table_rec_campaign.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });

    $('input[name="from_date"]').on('change', function() {
        table_rec_campaign.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
    });
    $('input[name="to_date"]').on('change', function() {
        table_rec_campaign.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
    });

    
    if ($('#pur_order-expense-form').length > 0) {
          expenseDropzone = new Dropzone("#pur_order-expense-form", appCreateDropzoneOptions({
              autoProcessQueue: false,
              clickable: '#dropzoneDragArea',
              previewsContainer: '.dropzone-previews',
              addRemoveLinks: true,
              maxFiles: 1,
              success: function(file, response) {
                  if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                      window.location.reload();
                  }
              }
        }));
    }

    appValidateForm($('#pur_order-expense-form'), {
          category: 'required',
          date: 'required',
          amount: 'required'
    }, projectExpenseSubmitHandler);


})(jQuery);



function convert_expense(pur_order,total){
    "use strict";

    $.post(admin_url + 'purchase/get_project_info/'+pur_order).done(function(response){
      response = JSON.parse(response);
      $('select[name="project_id"]').val(response.project_id).change();
      $('select[name="clientid"]').val(response.customer).change();
      $('select[name="currency"]').val(response.currency).change();
    });

    $('#pur_order_expense').modal('show');
    $('input[id="amount"]').val(total);
    $('#pur_order_additional').html('');
    $('#pur_order_additional').append(hidden_input('pur_order',pur_order));
}

function projectExpenseSubmitHandler(form) {
    "use strict";
      $.post(form.action, $(form).serialize()).done(function(response) {
          response = JSON.parse(response);
          if (response.expenseid) {
              if (typeof(expenseDropzone) !== 'undefined') {
                  if (expenseDropzone.getQueuedFiles().length > 0) {
                      expenseDropzone.options.url = admin_url + 'expenses/add_expense_attachment/' + response.expenseid;
                      expenseDropzone.processQueue();
                  } else {
                      window.location.assign(response.url);
                  }
              } else {
                  window.location.assign(response.url);
              }
          } else {
              window.location.assign(response.url);
          }
      });
      return false;
}

function change_delivery_status(status, id){
  "use strict";
  if(id > 0){
    $.post(admin_url + 'purchase/change_delivery_status/'+status+'/'+id).done(function(response){
      response = JSON.parse(response);
      if(response.success == true){
        if($('#status_span_'+id).hasClass('label-danger')){
          $('#status_span_'+id).removeClass('label-danger');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }else if($('#status_span_'+id).hasClass('label-success')){
          $('#status_span_'+id).removeClass('label-success');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }else if($('#status_span_'+id).hasClass('label-info')){
          $('#status_span_'+id).removeClass('label-info');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }else if($('#status_span_'+id).hasClass('label-warning')){
          $('#status_span_'+id).removeClass('label-warning');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }
        alert_float('success', response.mess);
      }else{
        alert_float('warning', response.mess);
      }
    });
  }
}

</script>