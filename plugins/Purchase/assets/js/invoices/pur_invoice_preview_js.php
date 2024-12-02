<script>
  

function preview_purinv_btn(invoker){
  "use strict"; 
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_purinv_file(id, rel_id);
}

function view_purinv_file(id, rel_id) {
  "use strict"; 
      $('#purinv_file_data').empty();
      $("#purinv_file_data").load(admin_url + 'purchase/file_purinv/' + id + '/' + rel_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }
      });
}
function close_modal_preview(){
  "use strict"; 
 $('._project_file').modal('hide');
}

function delete_purinv_attachment(id) {
  "use strict"; 
    if (confirm_delete()) {
        requestGet("<?php echo get_uri('purchase/delete_purinv_attachment/'); ?>" + id).done(function(success) {
            if (success == 1) {
                $("#purinv_pv_file").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function(error) {
            appAlert.warning(error.responseText);
        });
    }
  }

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