<script>

$(document).ready(function () {
  "use strict";
  $(".select2").select2();
});

  var estimate_id = '<?php echo html_entity_decode($estimate->id); ?>';
(function($) {
"use strict"; 
   var data_send_mail = {};
  <?php if(isset($send_mail_approve)){ 
    ?>
    data_send_mail = <?php echo json_encode($send_mail_approve); ?>;
    data_send_mail.rel_id = <?php echo html_entity_decode($estimate->id); ?>;
    data_send_mail.rel_type = 'pur_quotation';
    data_send_mail.addedfrom = <?php echo html_entity_decode($estimate->addedfrom); ?>;
    $.get("<?php echo get_uri('purchase/send_mail'); ?>", data_send_mail).done(function(response){
    });
  <?php } ?>


$('.show_approve').on('click', function() {
    "use strict";

    $('#approve_modal').modal('show');
  });

})(jQuery);

 function send_quotation(id) {
  "use strict"; 
  $('#additional_quo').html('');
  $('#additional_quo').append(hidden_input('pur_estimate_id',id));
  $('#send_quotation').modal('show');
 }


function change_status_pur_estimate(invoker,id){
  "use strict";
   $.post("<?php echo get_uri('purchase/change_status_pur_estimate/'); ?>" +invoker.value+'/'+id).done(function(reponse){
    reponse = JSON.parse(reponse);
    window.location.reload();
    appAlert.success(response.result);
  });
}

function send_request_approve(id){
  "use strict";
    var data = {};
    data.rel_id = <?php echo html_entity_decode($estimate->id); ?>;
    data.rel_type = 'pur_quotation';
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

function change_request_approval_status(id, status, sign_code = false){
  "use strict";
    var data = {};
    data.rel_id = id;
    data.rel_type = 'pur_quotation';
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

//preview purchase order attachment
function preview_estimate_btn(invoker){
  "use strict"; 
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_estimate_file(id, rel_id);
}

function view_estimate_file(id, rel_id) {
  "use strict"; 
      $('#estimate_file_data').empty();
      $("#estimate_file_data").load(admin_url + 'purchase/file_pur_estimate/' + id + '/' + rel_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }
      });
}
function close_modal_preview(){
  "use strict"; 
 $('._project_file').modal('hide');
}

function delete_estimate_attachment(id) {
  "use strict"; 
    if (confirm_delete()) {
        requestGet("<?php echo get_uri('purchase/delete_estimate_attachment/'); ?>" + id).done(function(success) {
            if (success == 1) {
                $("#estimate_pv_file").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function(error) {

            appAlert.warning(response.message);
        });
    }
  }

function add_contract_comment() {
  "use strict";
    var comment = $('#comment').val();
    if (comment == '') {
       return;
    }
    var data = {};
    data.content = comment;
    data.rel_id = estimate_id;
    data.rel_type = 'pur_quotation';
    $('body').append('<div class="dt-loader"></div>');
    $.post(admin_url + 'purchase/add_comment', data).done(function (response) {
       response = JSON.parse(response);
       $('body').find('.dt-loader').remove();
       if (response.success == true) {
          $('#comment').val('');
          get_contract_comments();
       }
    });
   }

   function get_contract_comments() {
    "use strict";
    if (typeof (estimate_id) == 'undefined') {
       return;
    }
    requestGet('purchase/get_comments/' + estimate_id+'/pur_quotation').done(function (response) {
       $('#contract-comments').html(response);
       var totalComments = $('[data-commentid]').length;
       var commentsIndicator = $('.comments-indicator');
       if(totalComments == 0) {
            commentsIndicator.addClass('hide');
       } else {
         commentsIndicator.removeClass('hide');
         commentsIndicator.text(totalComments);
       }
    });
   }

   function remove_contract_comment(commentid) {
    "use strict";
    if (confirm_delete()) {
       requestGetJSON('purchase/remove_comment/' + commentid).done(function (response) {
          if (response.success == true) {

            var totalComments = $('[data-commentid]').length;

             $('[data-commentid="' + commentid + '"]').remove();

             var commentsIndicator = $('.comments-indicator');
             if(totalComments-1 == 0) {
               commentsIndicator.addClass('hide');
            } else {
               commentsIndicator.removeClass('hide');
               commentsIndicator.text(totalComments-1);
            }
          }
       });
    }
   }

   function edit_contract_comment(id) {
    "use strict";
    var content = $('body').find('[data-contract-comment-edit-textarea="' + id + '"] textarea').val();
    if (content != '') {
       $.post(admin_url + 'purchase/edit_comment/' + id, {
          content: content
       }).done(function (response) {
          response = JSON.parse(response);
          if (response.success == true) {
             alert_float('success', response.message);
             $('body').find('[data-contract-comment="' + id + '"]').html(nl2br(content));
          }
       });
       toggle_contract_comment_edit(id);
    }
   }

   function toggle_contract_comment_edit(id) {
    "use strict";
       $('body').find('[data-contract-comment="' + id + '"]').toggleClass('hide');
       $('body').find('[data-contract-comment-edit-textarea="' + id + '"]').toggleClass('hide');
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