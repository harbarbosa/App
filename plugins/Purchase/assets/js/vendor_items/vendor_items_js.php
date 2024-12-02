<script>
$(document).ready(function () {
	$(".select2").select2();
});

(function($) {
  "use strict";

  group_it_change();
})(jQuery);

function group_it_change() {
"use strict";
var group = $('select[name="group_item"]').val();
if(group != ''){
  requestGet("<?php echo get_uri('purchase/group_it_change/') ;?>"+group).done(function(response){
    response = JSON.parse(response);
    if(response.html != ''){
      $('select[id="items"]').html('');
      $('select[id="items"]').append(response.html);
      $('.select2').select2('destroy');
      $(".select2").select2();
    }else{
      init_ajax_search('items','#items.ajax-search',undefined, "<?php echo get_uri('purchase/pur_commodity_code_search_vendor_item/purchase_price/can_be_purchased/'); ?>" +group);
      $('select[id="items"]').html('');
      $('.select2').select2('destroy');
      $(".select2").select2();
    }
  });
}else{
  init_ajax_search('items','#items.ajax-search',undefined, "<?php echo get_uri('purchase/pur_commodity_code_search'); ?>" );
  requestGet("<?php echo get_uri('purchase/group_it_change/'); ?>" +group).done(function(response){
    response = JSON.parse(response);
    if(response.html != ''){
      $('select[id="items"]').html('');
      $('select[id="items"]').append(response.html);
      $('.select2').select2('destroy');
      $(".select2").select2();
    }else{
      $('select[id="items"]').html('');
      $('.select2').select2('destroy');
      $(".select2").select2();
    }
  });
}
}

</script>