<script>
var pur_order_id = '<?php echo html_entity_decode($estimate->id); ?>';

$(document).ready(function () {
  "use strict"; 
  $(".select2").select2();
});

(function($) {
  "use strict"; 
   var data_send_mail = {};
  <?php if(isset($send_mail_approve)){ 
    ?>
    data_send_mail = <?php echo json_encode($send_mail_approve); ?>;
    data_send_mail.rel_id = <?php echo html_entity_decode($estimate->id); ?>;
    data_send_mail.rel_type = 'pur_order';
    data_send_mail.addedfrom = <?php echo html_entity_decode($estimate->addedfrom); ?>;
    $.get("<?php echo get_uri('purchase/send_mail'); ?>", data_send_mail).done(function(response){
    });
  <?php } ?>
$('.show_approve').on('click', function() {
    "use strict";

    $('#approve_modal').modal('show');
  });

})(jQuery);

 function send_po(id) {
  "use strict"; 
  $('#additional_po').html('');
  $('#additional_po').append(hidden_input('po_id',id));
  $('#send_po').modal('show');
 }

function add_payment(id){
  "use strict"; 
   appValidateForm($('#purorder-add_payment-form'),{amount:'required', date:'required'});
   $('#payment_record_pur').modal('show');
   $('.edit-title').addClass('hide');
   $('#additional').html('');
}

function add_payment_with_inv(id){
  "use strict"; 
  appValidateForm($('#purorder-add_payment_with_inv-form'),{pur_invoice:'required', amount:'required', date:'required'});
  $('#payment_record_pur_with_inv').modal('show');
  $('#inv_additional').html('');
}


function pur_inv_payment_change(el){
  "use strict"; 
  var invoice = $(el).val();
  if(invoice != '' ){
    $.post(admin_url+'purchase/pur_inv_payment_change/'+invoice).done(function(reponse){
      reponse = JSON.parse(reponse);
      $('#payment_record_pur_with_inv input[name="amount"]').val(reponse.amount);
      $('#payment_record_pur_with_inv input[name="amount"]').attr('max', reponse.amount);
    });
  }else{
    $('#payment_record_pur_with_inv input[name="amount"]').val(0);
    $('#payment_record_pur_with_inv input[name="amount"]').attr('max', 0);

    alert_float('warning', '<?php echo _l('please_select_purchase_invoice'); ?>');
  }
}

   
function change_status_pur_order(invoker,id){
  "use strict"; 
   $.post("<?php echo get_uri('purchase/change_status_pur_order/'); ?>" +invoker.value+'/'+id).done(function(reponse){
    reponse = JSON.parse(reponse);
    window.location.reload();
    appAlert.success(response.result);
  });
}

//preview purchase order attachment
function preview_purorder_btn(invoker){
  "use strict"; 
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_purorder_file(id, rel_id);
}

function view_purorder_file(id, rel_id) {
  "use strict"; 
      $('#purorder_file_data').empty();
      $("#purorder_file_data").load(admin_url + 'purchase/file_purorder/' + id + '/' + rel_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }
      });
}
function close_modal_preview(){
  "use strict"; 
 $('._project_file').modal('hide');
}

function delete_purorder_attachment(id) {
  "use strict"; 
    if (confirm_delete()) {
        requestGet("<?php echo get_uri('purchase/delete_purorder_attachment/'); ?>" + id).done(function(success) {
            if (success == 1) {
                $("#purorder_pv_file").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function(error) {
            appAlert.warning(error.responseText);
        });
    }
  }



function send_request_approve(id){
  "use strict";
    var data = {};
    data.rel_id = <?php echo html_entity_decode($estimate->id); ?>;
    data.rel_type = 'pur_order';
    data.addedfrom = <?php echo html_entity_decode($estimate->addedfrom); ?>;
  $("body").append('<div class="dt-loader"></div>');
    $.post("<?php echo get_uri('purchase/send_request_approve'); ?>", data).done(function(response){
        response = JSON.parse(response);
        $("body").find('.dt-loader').remove();
        if (response.success === true || response.success == 'true') {
            appAlert.success(response.message);
            window.location.reload();
        }else{

          appAlert.warning(response.message);
            window.location.reload();
        }
    });
}
$(function(){
  "use strict";
   SignaturePad.prototype.toDataURLAndRemoveBlanks = function() {
     var canvas = this._ctx.canvas;
       // First duplicate the canvas to not alter the original
       var croppedCanvas = document.createElement('canvas'),
       croppedCtx = croppedCanvas.getContext('2d');

       croppedCanvas.width = canvas.width;
       croppedCanvas.height = canvas.height;
       croppedCtx.drawImage(canvas, 0, 0);

       // Next do the actual cropping
       var w = croppedCanvas.width,
       h = croppedCanvas.height,
       pix = {
         x: [],
         y: []
       },
       imageData = croppedCtx.getImageData(0, 0, croppedCanvas.width, croppedCanvas.height),
       x, y, index;

       for (y = 0; y < h; y++) {
         for (x = 0; x < w; x++) {
           index = (y * w + x) * 4;
           if (imageData.data[index + 3] > 0) {
             pix.x.push(x);
             pix.y.push(y);

           }
         }
       }
       pix.x.sort(function(a, b) {
         return a - b
       });
       pix.y.sort(function(a, b) {
         return a - b
       });
       var n = pix.x.length - 1;

       w = pix.x[n] - pix.x[0];
       h = pix.y[n] - pix.y[0];
       var cut = croppedCtx.getImageData(pix.x[0], pix.y[0], w, h);

       croppedCanvas.width = w;
       croppedCanvas.height = h;
       croppedCtx.putImageData(cut, 0, 0);

       return croppedCanvas.toDataURL();
     };


 function signaturePadChanged() {
  "use strict"; 
   var input = document.getElementById('signatureInput');
   var $signatureLabel = $('#signatureLabel');
   $signatureLabel.removeClass('text-danger');

   if (signaturePad.isEmpty()) {
     $signatureLabel.addClass('text-danger');
     input.value = '';
     return false;
   }

   $('#signatureInput-error').remove();
   var partBase64 = signaturePad.toDataURLAndRemoveBlanks();
   partBase64 = partBase64.split(',')[1];
   input.value = partBase64;
 }

 var canvas = document.getElementById("signature");
 var signaturePad = new SignaturePad(canvas, {
  maxWidth: 2,
  onEnd:function(){
    signaturePadChanged();
  }
});

$('#identityConfirmationForm').submit(function() {
   signaturePadChanged();
 });
});

function signature_clear(){
"use strict";
var canvas = document.getElementById("signature");
var signaturePad = new SignaturePad(canvas, {
  maxWidth: 2,
  onEnd:function(){

  }
});
signaturePad.clear();
$('input[name="signature"]').val('');
}
function sign_request(id){
  "use strict"; 
  var signature = $('input[name="signature"]').val();
  if(signature == ''){
    appAlert.warning('Please sign the form!');
  }else{
    change_request_approval_status(id,2, true);
  }
}
function approve_request(id){
  "use strict";
  change_request_approval_status(id,2);
}
function deny_request(id){
  "use strict";
    change_request_approval_status(id,3);
}
function change_request_approval_status(id, status, sign_code){
  "use strict";
    var data = {};
    data.rel_id = id;
    data.rel_type = 'pur_order';
    data.approve = status;
    if(sign_code == true){
      data.signature = $('input[name="signature"]').val();
    }else{
      data.note = $('textarea[name="reason"]').val();
    }
    $.post("<?php echo get_uri('purchase/approve_request/'); ?>" + id, data).done(function(response){
        response = JSON.parse(response); 
        if (response.success === true || response.success == 'true') {
            appAlert.success(response.message);
            window.location.reload();
        }
    });
}
function accept_action() {
  "use strict";
  $('#add_action').modal('show');
}

function convert_to_purchase_inv(pur_order){
  "use strict";
  $.post(admin_url + 'purchase/convert_po_payment/' + pur_order).done(function(response){
      response = JSON.parse(response);
      if(response.success == true){
        appAlert.success(response.mess);
      }else{
        alert_float('warning', response.mess);
      }  
  });
}


// Will give alert to confirm delete
function confirm_delete() {
  "use strict"; 
    var message = 'Are you sure you want to perform this action?';

    // Clients area
    if (typeof(app) != 'undefined') {
        message = "<?php echo app_lang('confirm_action_prompt'); ?>";
    }

    var r = confirm(message);
    if (r == false) { return false; }
    return true;
}
</script>