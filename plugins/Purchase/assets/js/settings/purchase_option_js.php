<script type="text/javascript">

 function purchase_order_setting(invoker){
    "use strict";
    var input_name = invoker.value;
    var input_name_status = $('input[id="'+invoker.value+'"]').is(":checked");
    
    var data = {};
        data.input_name = input_name;
        data.input_name_status = input_name_status;
    $.post('<?php echo get_uri('purchase/purchase_order_setting'); ?>', data).done(function(response){
          response = JSON.parse(response); 
          if (response.success == true) {
              appAlert.success(response.message);
          }else{
              appAlert.error(response.message);

          }
      });

}

function item_by_vendor(invoker){
  "use strict";
    var input_name = invoker.value;
    var input_name_status = $('input[id="'+invoker.value+'"]').is(":checked");
    
    var data = {};
        data.input_name = input_name;
        data.input_name_status = input_name_status;
    $.post('<?php echo get_uri('purchase/item_by_vendor'); ?>', data).done(function(response){
          response = JSON.parse(response); 
          if (response.success == true) {
              appAlert.success(response.message);
          }else{
              appAlert.error(response.message);

          }
      });
}

function show_tax_column(invoker){
  "use strict";
    var input_name = invoker.value;
    var input_name_status = $('input[id="'+invoker.value+'"]').is(":checked");
    
    var data = {};
        data.input_name = input_name;
        data.input_name_status = input_name_status;
    $.post('<?php echo get_uri('purchase/show_tax_column'); ?>', data).done(function(response){
          response = JSON.parse(response); 
          if (response.success == true) {
              appAlert.success(response.message);
          }else{
              appAlert.error(response.message);

          }
      });
}

function send_email_welcome_for_new_contact(invoker){
  "use strict";
    var input_name = invoker.value;
    var input_name_status = $('input[id="'+invoker.value+'"]').is(":checked");
    
    var data = {};
        data.input_name = input_name;
        data.input_name_status = input_name_status;
    $.post('<?php echo get_uri('purchase/send_email_welcome_for_new_contact'); ?>', data).done(function(response){
          response = JSON.parse(response); 
          if (response.success == true) {
              appAlert.success(response.message);
          }else{
              appAlert.error(response.message);

          }
      });
}

function reset_purchase_order_number_every_month(invoker){
  "use strict";
    var input_name = invoker.value;
    var input_name_status = $('input[id="'+invoker.value+'"]').is(":checked");
    
    var data = {};
        data.input_name = input_name;
        data.input_name_status = input_name_status;
    $.post('<?php echo get_uri('purchase/reset_purchase_order_number_every_month'); ?>', data).done(function(response){
          response = JSON.parse(response); 
          if (response.success == true) {
              appAlert.success(response.message);
          }else{
              appAlert.error(response.message);

          }
      });
}

function po_only_prefix_and_number(invoker){
  "use strict";
    var input_name = invoker.value;
    var input_name_status = $('input[id="'+invoker.value+'"]').is(":checked");
    
    var data = {};
        data.input_name = input_name;
        data.input_name_status = input_name_status;
    $.post('<?php echo get_uri('purchase/po_only_prefix_and_number'); ?>', data).done(function(response){
          response = JSON.parse(response); 
          if (response.success == true) {
              appAlert.success(response.message);
          }else{
              appAlert.error(response.message);

          }
      });
}
</script>